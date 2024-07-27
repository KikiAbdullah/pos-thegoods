<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Controllers\Traits\TransaksiTrait;
use App\Http\Controllers\Traits\WhatsappTrait;
use App\Models\Kasir;
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
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    use WhatsappTrait;
    use TransaksiTrait;

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

    public function ajaxDataOrdered()
    {

        $kasir = Kasir::where([
            'tanggal' => date('Y-m-d'),
            'status'    => 'open',
        ])->orderBy('id', 'desc')->first();


        if ($this->withTrashed) {
            $mapped             = $this->model->where(['kasir_id' => $kasir->id, 'status' => 'ordered'])->withTrashed();
        } else {
            $mapped             = $this->model->where(['kasir_id' => $kasir->id, 'status' => 'ordered']);
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

    public function ajaxDataToday()
    {

        $kasir = Kasir::where([
            'tanggal' => date('Y-m-d'),
            'status'    => 'open',
        ])->orderBy('id', 'desc')->first();


        if ($this->withTrashed) {
            $mapped             = $this->model->where([
                'kasir_id' => $kasir->id,
                'tanggal'   => date('Y-m-d')
            ])->where('status', '!=', 'open')->withTrashed();
        } else {
            $mapped             = $this->model->where([
                'kasir_id' => $kasir->id,
                'tanggal'   => date('Y-m-d')
            ])->where('status', '!=', 'open');
        }

        return DataTables::of($mapped)
            ->addColumn('status_formatted', function ($item) {
                return $item->status_formatted;
            })
            ->addColumn('tanggal_formatted', function ($item) {
                return $item->tanggal_formatted;
            })
            ->addColumn('total', function ($item) {
                return cleanNumber($item->total);
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
                $button['payment']      = $this->generateUrl('change-status');
                $button['upload-url-form']      = $this->generateUrl('upload-url-form');
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

        $no                         = $this->gen_number($this->model, 'no', 'TR$$@@###', $model->tanggal, 'tanggal', true);
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
                    $msg = 'Pemesanan nomor Transaksi ' . $transaction->no . ' telah dibuat oleh ' . auth()->user()->name;
                    $logMsg = 'Pemesanan nomor Transaksi <b>' . $transaction->no . '</b> telah dibuat oleh <b>' . auth()->user()->name . '</b>';

                    if ($transaction->packages->isEmpty()) {
                        DB::rollback();

                        return response()->json(responseFailed('Package harus diisi'));
                    }

                    $transaction->update([
                        'status' => 'ordered',
                        'ordered_at' => date('Y-m-d H:i:s'),
                        'ordered_by' => auth()->user()->id,
                    ]);

                    break;

                case 'payment':
                    $msg            = 'Nomor Transaksi ' . $transaction->no . ' dilanjutkan ke pembayaran oleh ' . auth()->user()->name;
                    $logMsg         = 'Nomor Transaksi <b>' . $transaction->no . '</b> dilanjutkan ke pembayaran oleh <b>' . auth()->user()->name . '</b>';


                    foreach ($transaction->packages as $package) {
                        if (empty($package->url)) {
                            DB::rollback();

                            return response()->json(responseFailed('Pastikan URL Google Drive telah diinputkan'));
                        }
                    }

                    $transaction->update([
                        'status' => 'payment',
                        'payment_at' => date('Y-m-d H:i:s'),
                        'payment_by' => auth()->user()->id,
                    ]);

                    break;

                case 'verify':

                    $this->saveTrans($transaction);

                    //$this->sendInvoiceToWhatsapp($transaction);

                    $total = $transaction->total;
                    $amountPaid = str_replace(',', '', $data['amount_paid']);
                    if ($amountPaid <  $total) {
                        DB::rollback();

                        return response()->json(responseFailed('Nominal yang dibayarkan kurang dari total : Rp ' . $total));
                    }

                    $kembalian = $amountPaid - $total;

                    $statusEdited   = 'verify';
                    $msg            = $kembalian > 0 ? 'Kembalian : Rp' . cleanNumber($kembalian) : 'Verifikasi Pembayaran pada Nomor Transaksi ' . $transaction->no;
                    $logMsg         = 'Verifikasi Pembayaran pada Nomor Transaksi <b>' . $transaction->no . '</b>';


                    $data['verify_at']  = date('Y-m-d H:i:s');
                    $data['verify_by']  = auth()->user()->id;
                    $transaction->update($data);
                    break;

                case 'rejected':

                    if (empty($data['keterangan'])) {
                        DB::rollback();

                        return response()->json(responseFailed('Keterangan harus diisi'));
                    }


                    $this->deleteTrans($transaction);

                    $msg            = 'Pembatalan pada Nomor transaksi ' . $transaction->no;
                    $logMsg         = 'Pembatalan pada Nomor transaksi <b>' . $transaction->no . '</b>';

                    $transaction->update([
                        'status' => 'rejected',
                        'text_rejected' => $data['keterangan'],
                        'rejected_at' => date('Y-m-d H:i:s'),
                        'rejected_by' => auth()->user()->id,
                    ]);
                    break;
            }

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

                case 'unpayment':
                    $statusEdited = 'ordered';
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

    public function uploadUrlForm(Request $request, $id)
    {
        $model                      = $this->model->with($this->relation)->find($id);

        $data['id']                 = $id;
        $data['url']                = $this->generateUrl('upload-url');
        $data['item']               = $model;

        return view($this->generateViewName('upload-url-form'))->with($data);
    }

    public function uploadUrl(Request $request, $id)
    {
        try {

            DB::beginTransaction();

            $data                    = $request->all();

            $transaction = Transaction::find($id);

            //addons
            if (!empty(array_filter($data['url']))) {
                $addons = [];
                foreach (array_filter($data['url']) as $package_id => $url) {
                    $transPackage = TransactionPackage::find($package_id);

                    $transPackage->update([
                        'url' => $url,
                    ]);
                }
            }

            //addons

            $this->logHelper->storeLogMsg('Penambahan URL pada Nomor Transaksi <b>' . $transaction->no . '</b>', 'update');

            DB::commit();

            return response()->json(responseSuccess());
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }
    //PROCESS


    ///WHATSAPP TEXT
    public function sendInvoiceToWhatsapp($trans)
    {
        $invoicePdf     = $this->makeInvoicePdf();

        $subject        = "Hai Kak " . ucwords($trans->customer_name);
        $textWa         = $this->textWhatsapp($trans);
        $footer         = "\nBest Regards,\nThe Good Studios";

        $this->sendWhatsapp('085155300552', $subject, $textWa, $footer);
        $this->sendWhatsappAttachment('085155300552', $subject, $textWa, $footer, public_path('storage/invoice/' . $invoicePdf));
    }

    public function textWhatsapp($trans)
    {
        $text = "";

        foreach ($trans->packages as $package) {
            $text .= $package->package_name . " : " . $package->url . " \n";
        }

        $text .= "\nBerikut untuk link soft file dari hasil foto The Good Studios\n";
        $text .= "Untuk link google drive berlaku setelah pesan ini terkirim & jangan lupa buat ulas kami di google drive ya kak..";

        return $text;
    }

    public function makeInvoicePdf()
    {
        $view = [
            'title'         => 'INVOICE',
        ];

        $pdf = Pdf::loadView($this->generateViewName('print'), $view);
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        $content    = $pdf->download()->getOriginalContent();
        $pdf_name   = $this->makeFileName();


        $file =   Storage::put('public/invoice/' . $pdf_name, $content);

        return $pdf_name;
    }

    public function makeFileName()
    {
        $title          = formatDate('Y-m-d', 'F_Y', date('Y-m-d')) . '-report_reminder.pdf';

        return $title;
    }
    ///WHATSAPP TEXT

}
