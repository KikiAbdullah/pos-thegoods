<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Controllers\Traits\WhatsappTrait;
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
        return array(
            'list_package'   => $this->listPackageData(),
            'list_addon'    => $this->listAddonData(),
            'list_customer_today' => $this->listCustomerToday(),
            'list_transaction_today' => $this->listTransProcessToday(),
        );
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
        $data['grandTotal']        = $grandTotal;

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
        $model                      = $this->model->where('tanggal', date('Y-m-d'))
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

        $jml                = Transaction::where([
            'status' => 'open',
            'tanggal' => date('Y-m-d'),
        ])
            ->where(function ($query) use ($request) {
                $query->where("customer_name", 'LIKE', "%" . $request->q . "%");
            })
            ->count();

        $pagination         = $this->selectPaginationAttr($request, $jml);

        $data               = Transaction::where([
            'status' => 'open',
            'tanggal' => date('Y-m-d'),
        ])
            ->where(function ($query) use ($request) {
                $query->where("customer_name", 'LIKE', "%" . $request->q . "%");
            })
            ->offset($pagination['offset'])
            ->limit($pagination['paginate'])
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'text'          => $item->customer_name,
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

    public function formCustomer(Request $request)
    {
        $data['url']                = $this->generateUrl('store-customer');
        $data['form']                = $this->generateUrl('form');
        $data['data']               = [];

        return view($this->generateViewName('form-customer'))->with($data);
    }

    public function storeCustomer(Request $request)
    {
        try {

            DB::beginTransaction();

            $data  = $this->getRequest();

            $model = $this->model->fill($data);
            $model->save();

            if (method_exists($this, 'customStore')) {
                $this->customStore($data, $model);
            }

            $log_helper     = new LogHelper;

            $log_helper->storeLog('add', $model->no ?? $model->id, $this->subtitle);

            DB::commit();
            $response           = [
                'status'            => true,
                'msg'               => 'Data Saved.',
                'data'              => $model,
            ];
            return response()->json($response);
        } catch (Exception $e) {

            DB::rollback();
            $response           = [
                'status'            => false,
                'msg'               => $e->getMessage(),
            ];
            return response()->json($response);
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
