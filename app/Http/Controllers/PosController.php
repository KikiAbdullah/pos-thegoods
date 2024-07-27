<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Controllers\Traits\WhatsappTrait;
use App\Models\Kasir;
use App\Models\Master\Addon;
use App\Models\Master\Package;
use App\Models\Transaction;
use App\Models\TransactionAddon;
use App\Models\TransactionPackage;
use Illuminate\Http\Request;
use DB;

class PosController extends Controller
{
    use WhatsappTrait;

    public function __construct(Transaction $model, LogHelper $logHelper)
    {
        $this->title            = 'POS';
        $this->subtitle         = 'POS';
        $this->model_request    = Request::class;
        $this->folder           = '';
        $this->relation         = ['packages', 'addons'];
        $this->model            = $model;
        $this->logHelper        = $logHelper;
        $this->withTrashed      = false;
    }


    public function formData()
    {
        $kasir = Kasir::where([
            'tanggal' => date('Y-m-d'),
            'status'    => 'open',
        ])->orderBy('id', 'desc')->first();

        return array(
            'list_package'              => $this->listPackageData(),
            'list_addon'                => $this->listAddonData(),
            'list_transaction_today'    => $this->listTransProcessToday(),
            'list_tipe_pembayaran'     => $this->listTipePembayaran(),
            'kasir'                     => $kasir,
        );
    }

    public function index(Request $request)
    {
        $view  = [
            'title'             => $this->title,
            'subtitle'          => $this->subtitle,
            'folder'            => $this->folder ?? '',
            'items'             => method_exists($this, 'ajaxData') ? null : $this->indexData($this->withTrashed),
            'url'               => array_merge(['store' => $this->generateUrl('store'), 'edit' => $this->generateUrl('edit'), 'destroy' => $this->generateUrl('destroy'), 'foto' => $this->generateUrl('foto')], $this->completeUrl()),
            'data'              => method_exists($this, 'formData') ? $this->formData() : null,
            'form'              => $this->generateViewName('form'),
        ];
        return view($this->generateViewName(__FUNCTION__))->with($view);
    }

    public function getTransaction(Request $request)
    {
        $data  = $request->all();

        $trans = Transaction::find($data['id']);

        if ($trans) {
            return response()->json(responseSuccess($trans));
        }

        return response()->json(responseFailed());
    }

    public function detailTransaction(Request $request)
    {
        $data  = $request->all();

        $trans = Transaction::find($data['id']);

        if ($trans) {

            $data['item']       = $trans;
            $data['grandTotal'] = $trans->packages->sum('harga') + $trans->addons->sum('total');
            $data['button']             = [
                'change-status'   => 'transaction.change-status',
            ];

            $view               = [
                'view'  => view($this->generateViewName('detail-transaction'))->with($data)->render(),
            ];

            return response()->json(responseSuccess($view));
        }

        return response()->json(responseFailed());
    }

    public function choosePackage(Request $request)
    {
        try {
            DB::beginTransaction();

            $data                    = $request->all();

            $transaction = Transaction::find($data['transaction_id']);

            //packages
            if ($data['type'] == 'package') {
                $package = Package::find($data['id']);

                TransactionPackage::create([
                    'transaction_id' => $transaction->id,
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'harga' => $package->harga,
                ]);
            }
            //packages

            //addons
            if ($data['type'] == 'addon') {
                $addon = Addon::find($data['id']);

                $transAddon = TransactionAddon::where([
                    'transaction_id' => $transaction->id,
                    'addon_id' => $addon->id,
                ])->first();

                if (empty($transAddon)) {
                    TransactionAddon::create([
                        'transaction_id' => $transaction->id,
                        'addon_id' => $addon->id,
                        'addon_name' => $addon->name,
                        'qty' => 1,
                        'harga' => $addon->harga,
                    ]);
                } else {
                    $transAddon->update([
                        'qty' => $transAddon->qty + 1,
                    ]);
                }
            }
            //addons

            $this->logHelper->storeLogMsg('Penambahan Lines pada menu <b>' . $this->title . '</b> dengan nomor <b>' . $transaction->no . '</b>', 'add');

            DB::commit();

            return response()->json(responseSuccess($transaction));
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }

    public function deleteAddon($transPackageId)
    {
        try {

            DB::beginTransaction();

            $trans              = TransactionAddon::with(['transaction'])->find($transPackageId);

            if ($trans->transaction->status == 'ordered' || $trans->transaction->status == 'verify') {
                DB::rollback();
                return response()->json(responseFailed('Status Transaksi: ' . strtoupper($trans->transaction->status)));
            }

            if ($trans->qty > 1) {
                $trans->update([
                    'qty' => $trans->qty - 1,
                ]);
            } else {
                $trans->delete();
            }

            DB::commit();

            return response()->json(responseSuccess());
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }

    public function orderList(Request $request)
    {
        $model                      = $this->model->with($this->relation)->find($request->id);

        $grandTotal                 = $model->packages->sum('harga') + $model->addons->sum('total');

        $data['id']                 = $request->id;
        $data['item']               = $model;
        $data['grandTotal']         = $grandTotal;

        $data['button']             = [
            'change-status'   => 'transaction.change-status',
        ];

        $response                   = [
            'status'                    => true,
            'view'                      => view($this->generateViewName('order-list'))->with($data)->render(),
        ];
        return response()->json($response);
    }

    public function transToday(Request $request)
    {
        $kasir  = $this->kasirOpenToday();
        $model                      = $this->model
            ->where('kasir_id', $kasir->id)
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 'payment')
            ->orderBy('id', 'asc')
            ->get();

        $data['items']        = $model;

        $response                   = [
            'status'                    => true,
            'view'                      => view($this->generateViewName('trans-today'))->with($data)->render(),
        ];
        return response()->json($response);
    }


    //NEW CUSTOMER
    public function getCustomer(Request $request)
    {
        DB::connection()->disableQueryLog();

        $kasir = $this->kasirOpenToday();


        $jml                = Transaction::where([
            'status' => 'open',
            'tanggal' => date('Y-m-d'),
            'kasir_id' => $kasir->id,
        ])->orderBy('id', 'desc')
            ->where(function ($query) use ($request) {
                $query->where("customer_name", 'LIKE', "%" . $request->q . "%");
            })
            ->count();

        $pagination         = $this->selectPaginationAttr($request, $jml);

        $data               = Transaction::where([
            'status' => 'open',
            'tanggal' => date('Y-m-d'),
            'kasir_id' => $kasir->id,
        ])->orderBy('id', 'desc')
            ->where(function ($query) use ($request) {
                $query->where("customer_name", 'LIKE', "%" . $request->q . "%");
            })
            ->offset($pagination['offset'])
            ->limit($pagination['paginate'])
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'text'          => ucwords($item->customer_name),
                    'no'            => $item->no,
                ];
            });

        return [
            "results"           => $data,
            "pagination"        => [
                "more"  => $pagination['more'],
            ]
        ];
    }

    public function selectPaginationAttr($request, $jumlah_data, $paginate = 20)
    {
        $page       = $request->page ?? 1;
        $page--;
        $offset     = $page * $paginate;
        $more       = false;

        if ($jumlah_data > ($paginate + $offset)) {
            $more = true;
        }

        return [
            'offset'                => $offset,
            'paginate'              => $paginate,
            'more'                  => $more,
        ];
    }

    public function createCustomer(Request $request)
    {

        $data['url']        = $this->generateUrl('customer.store');
        $data['form']       = $this->generateViewName('customer.form');
        $data['data']       = [
            'list_tipe_pembayaran' => $this->listTipePembayaran(),
        ];

        return view($this->generateViewName('customer.create'))->with($data);
    }

    public function storeCustomer(Request $request)
    {
        try {

            DB::beginTransaction();

            $kasir  = $this->kasirOpenToday();

            $data  = $this->getRequest();

            $data['kasir_id']   = $kasir->id;
            $data['tanggal']    = date('Y-m-d');

            $model = $this->model->fill($data);
            $model->save();

            if (method_exists($this, 'customStore')) {
                $this->customStore($data, $model);
            }

            $log_helper     = new LogHelper;

            $log_helper->storeLog('add', $model->no ?? $model->id, $this->subtitle);

            DB::commit();
            return response()->json(responseSuccess($model, 'Customer berhasil dibuat, Customer: ' . ucwords($model->customer_name) . '(' . $model->no . ')'));
        } catch (Exception $e) {

            DB::rollback();
            return response()->json(responseFailed($e->getMessage()));
        }
    }

    public function customStore($data, $model)
    {
        $model->status              = "open";

        $no                         = $this->gen_number($this->model, 'no', 'TR$$@@###', $model->tanggal, 'tanggal', true);
        $model->no                  = $no;
        $model->save();
    }
    //NEW CUSTOMER

}
