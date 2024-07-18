<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Master\Addon;
use App\Models\Master\Package;
use App\Models\Transaction;
use App\Models\TransactionAddon;
use App\Models\TransactionPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use DB;

class TransactionController extends Controller
{
    public function __construct(Transaction $model, LogHelper $logHelper)
    {
        $this->title            = 'Transaction';
        $this->subtitle         = 'Transaction List';
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
            'list_package'   => $this->listPackage()
        );
    }

    public function ajaxData()
    {
        if ($this->withTrashed) {
            $mapped             = $this->model->withTrashed()->query();
        } else {
            $mapped             = $this->model->query();
        }

        return DataTables::of($mapped)
            ->addColumn('status_formatted', function ($item) {
                return $item->status_formatted;
            })
            ->addColumn('tanggal_formatted', function ($item) {
                return $item->tanggal_formatted;
            })
            ->rawColumns(['status_formatted'])
            ->toJson();
    }


    public function menuoption(Request $request)
    {
        $data         = $request->all();

        $trans      = $this->model->find($data['id']);

        $button     = [];


        switch ($trans->status) {
            case 'open':
                $button['edit']         = $this->generateUrl('edit');
                $button['destroy']      = $this->generateUrl('destroy');

                $button['ordered']      = $this->generateUrl('change-status');
                break;

            case 'ordered':
                $button['unordered']      = $this->generateUrl('unchange-status');
                $button['photoshoot']      = $this->generateUrl('change-status');
                break;

            case 'photoshoot':
                $button['unphotoshoot']      = $this->generateUrl('unchange-status');
                $button['payment']      = $this->generateUrl('change-status');

                break;

            case 'payment':
                $button['unpayment']      = $this->generateUrl('unchange-status');
                $button['verify']      = $this->generateUrl('change-status');
                break;

            case 'verify':
                $button['unverify']      = $this->generateUrl('unchange-status');
                break;
        }

        $view         = [
            'status'        => true,
            'view'          => view('transaction.menuoption')->with(['id' => $data['id'], 'button' => $button])->render(),
        ];

        return response()->json($view);
    }

    public function customStore($data, $model)
    {
        $model->status              = "open";

        $no                         = $this->gen_number($this->model, 'no', 'TRX$$-@@#####', $model->tanggal, 'tanggal', true);
        $model->no                  = $no;
        $model->save();
    }

    public function customDestroy($model)
    {
        $model->packages()->delete();
        $model->addons()->delete();

        return true;
    }


    ///LINES
    public function lines(Request $request)
    {
        $model                      = $this->model->with($this->relation)->find($request->id);

        $grandTotal                 = $model->packages->sum('harga') + $model->addons->sum('total');

        $data['id']                 = $request->id;
        $data['item']               = $model;
        $data['grandTotal']        = $grandTotal;

        $response                   = [
            'status'                    => true,
            'view'                      => view($this->generateViewName('lines'))->with($data)->render(),
        ];
        return response()->json($response);
    }

    public function formLines(Request $request)
    {
        $model                      = $this->model->with($this->relation)->find($request->id);

        $data['id']                 = $request->id;
        $data['url']                = $this->generateUrl('lines-add');
        $data['data']               = [
            'list_package'              => $this->listPackage(),
            'list_addon'                => $this->listAddonData(),
        ];
        $data['item']               = $model;

        return view($this->generateViewName('form-lines'))->with($data);
    }

    public function saveLines(Request $request, $id)
    {
        try {

            // $validator               = Validator::make($request->all(), [
            //     'package_id'         => 'required',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            // }

            DB::beginTransaction();

            $data                    = $request->all();

            $transaction = Transaction::find($id);

            //packages
            if (!empty($data['package_id'])) {
                $package = Package::find($data['package_id']);

                TransactionPackage::create([
                    'transaction_id' => $id,
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'harga' => $package->harga,
                ]);
            }
            //packages

            //addons
            if (!empty(array_filter($data['qty']))) {
                $addons = [];
                foreach (array_filter($data['qty']) as $addon_id => $qty) {
                    $addon = Addon::find($addon_id);

                    $addons[] = [
                        'transaction_id' => $id,
                        'addon_id' => $addon->id,
                        'addon_name' => $addon->name,
                        'qty' => $qty,
                        'harga' => $addon->harga,
                        'created_by' => auth()->user()->id,
                    ];
                }

                if (!empty($addons)) {
                    TransactionAddon::insert($addons);
                }
            }

            //addons

            $this->logHelper->storeLogMsg('Penambahan Lines pada menu <b>' . $this->title . '</b> dengan nomor <b>' . $transaction->no . '</b>', 'add');

            DB::commit();

            return response()->json(responseSuccess());
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }

    public function deletePackage($transPackageId)
    {
        try {

            DB::beginTransaction();

            $trans              = TransactionPackage::with(['transaction'])->find($transPackageId);

            if ($trans->transaction->status != 'open') {
                DB::rollback();
                return response()->json(responseFailed('Status Transaksi: ' . strtoupper($trans->transaction->status)));
            }

            $this->logHelper->storeLogMsg('Penghapusan Package pada menu <b>' . $this->title . '</b> dengan nomor <b>' . $trans->transaction->no . '</b>', 'delete');

            $trans->delete();

            DB::commit();

            return response()->json(responseSuccess());
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

            if ($trans->transaction->status != 'open') {
                DB::rollback();
                return response()->json(responseFailed('Status Transaksi: ' . strtoupper($trans->transaction->status)));
            }

            $this->logHelper->storeLogMsg('Penghapusan Add On pada menu <b>' . $this->title . '</b> dengan nomor <b>' . $trans->transaction->no . '</b>', 'delete');

            $trans->delete();

            DB::commit();

            return response()->json(responseSuccess());
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }
    ///LINES


    //PROCESS
    public function changeStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $data                    = $request->all();

            $transaction = Transaction::find($id);

            $statusEdited = $transaction->status;
            $msg = '';
            $logMsg = '';

            switch ($data['status']) {
                case 'ordered':
                    $statusEdited = 'ordered';
                    $msg = 'Pemesanan nomor transaksi ' . $transaction->no . ' telah diterima';
                    $logMsg = 'Pemesanan nomor transaksi <b>' . $transaction->no . '</b> telah diterima';
                    break;

                case 'photoshoot':
                    $statusEdited = 'photoshoot';
                    $msg = 'Nomor transaksi ' . $transaction->no . ' memulai sesi photo';
                    $logMsg = 'Nomor transaksi <b>' . $transaction->no . '</b> memulai sesi photo';
                    break;

                case 'payment':
                    $statusEdited = 'payment';
                    $msg = 'Nomor transaksi ' . $transaction->no . ' melakukan pembayaran';
                    $logMsg = 'Nomor transaksi <b>' . $transaction->no . '</b> melakukan pembayaran';
                    break;

                case 'verify':
                    $statusEdited = 'verify';
                    $msg = 'Nomor transaksi ' . $transaction->no . ' berhasil diverifikasi';
                    $logMsg = 'Nomor transaksi <b>' . $transaction->no . '</b> berhasil diverifikasi';
                    break;
            }

            $transaction->update([
                'status' => $statusEdited,
            ]);

            $this->logHelper->storeLogMsg($logMsg, 'add');

            DB::commit();

            return response()->json(responseSuccess([], $msg));
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }

    public function unChangeStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $data                    = $request->all();

            $transaction = Transaction::find($id);

            $statusEdited = $transaction->status;
            $msg = '';
            $logMsg = '';

            switch ($data['status']) {
                case 'unordered':
                    $statusEdited = 'open';
                    $msg = 'Pemesanan nomor transaksi ' . $transaction->no . ' telah dibatalkan';
                    $logMsg = 'Pemesanan nomor transaksi <b>' . $transaction->no . '</b> telah dibatalkan';
                    break;

                case 'unphotoshoot':
                    $statusEdited = 'ordered';
                    $msg = 'Nomor transaksi ' . $transaction->no . ' batal memulai sesi photo';
                    $logMsg = 'Nomor transaksi <b>' . $transaction->no . '</b> batal memulai sesi photo';
                    break;

                case 'unpayment':
                    $statusEdited = 'photoshoot';
                    $msg = 'Nomor transaksi ' . $transaction->no . ' batal melakukan pembayaran';
                    $logMsg = 'Nomor transaksi <b>' . $transaction->no . '</b> batal melakukan pembayaran';
                    break;

                case 'unverify':
                    $statusEdited = 'payment';
                    $msg = 'Nomor transaksi ' . $transaction->no . ' batal diverifikasi';
                    $logMsg = 'Nomor transaksi <b>' . $transaction->no . '</b> batal diverifikasi';
                    break;
            }

            $transaction->update([
                'status' => $statusEdited,
            ]);

            $this->logHelper->storeLogMsg($logMsg, 'edit');

            DB::commit();

            return response()->json(responseSuccess([], $msg));
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }
    //PROCESS
}
