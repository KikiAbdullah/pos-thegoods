<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Kasir;
use App\Models\Transaction;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class KasirController extends Controller
{
    public function __construct(Kasir $model, LogHelper $logHelper)
    {
        $this->title            = 'Kasir';
        $this->subtitle         = 'Kasir';
        $this->model_request    = Request::class;
        $this->folder           = '';
        $this->relation         = ['transaction', 'createdBy'];
        $this->model            = $model;
        $this->logHelper        = $logHelper;
        $this->withTrashed      = false;
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
            ->editColumn('open', function ($item) {
                return formatDate('Y-m-d H:i:s', 'H:i:s', $item->open);
            })
            ->editColumn('closed', function ($item) {
                return formatDate('Y-m-d H:i:s', 'H:i:s', $item->closed);
            })
            ->addColumn('created_name', function ($item) {
                return $item->createdBy->name;
            })
            ->rawColumns(['status_formatted'])
            ->toJson();
    }

    public function lines(Request $request)
    {
        $model                      = $this->model->with($this->relation)->find($request->id);


        $data['id']                 = $request->id;
        $data['item']               = $model;
        $data['data']               = [
            'list_tipe_pembayaran'  => $this->listTipePembayaran(),
        ];

        $response                   = [
            'status'                    => true,
            'view'                      => view($this->generateViewName('lines'))->with($data)->render(),
        ];
        return response()->json($response);
    }



    public function openView()
    {
        $data['url']                = 'pos.open-process';
        $data['form']               = $this->generateViewName('form');
        $data['data']               = [
            'list_user'              => $this->listUser(),
        ];

        return view($this->generateViewName(__FUNCTION__))->with($data);
    }

    public function openProcess(Request $request)
    {
        try {

            $validator               = Validator::make($request->all(), [
                'saldo_awal'      => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            }

            DB::beginTransaction();
            $data                   = $request->all();

            $data['tanggal']        = date('Y-m-d');
            $data['open']           = date('Y-m-d H:i:s');
            $data['status']         = 'open';

            $newKasir   = Kasir::create($data);

            $this->logHelper->storeLogMsg('Buka Kasir dengan saldo awal sebesar <b>Rp. ' . cleanNumber($newKasir->saldo_awal) . '</b>.', 'add');

            DB::commit();

            return response()->json(responseSuccess());
        } catch (Exception $e) {

            DB::rollback();

            return response()->json(responseFailed($e->getMessage()));
        }
    }
    ///TUTUP KASIR
    public function closedView(Request $request, $id)
    {
        $kasir  = $this->model->with($this->relation)->find($id);


        if ($kasir->transaction->isEmpty()) {
            $msgerror = 'Belum ada transaksi hari ini';
            $error = ValidationException::withMessages([
                'msg' => $msgerror
            ]);
            throw $error;
        }


        $view   = [
            'title' => 'Tutup ' . $this->title,
            'item'  => $kasir,
            'id'    => $id,
            'form'  => $this->generateViewName('form'),
            'url'   => [
                'closed'    => 'pos.closed-process',
            ],
            'data'   => [
                'list_tipe_pembayaran'    => $this->listTipePembayaran(),
            ],
        ];

        return view($this->generateViewName('closed'), $view);
    }

    public function closedProcess(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $data  = $this->getRequest();
            $model = $this->model->findOrFail($id);

            $hasilAkhir = str_replace(',', '', $data['total_transaksi']) - $model->saldo_awal;

            $data['close'] = date('Y-m-d H:i:s');
            $data['hasil_akhir'] = $hasilAkhir;
            $model->fill($data);

            $model->save();

            $log_helper     = new LogHelper;

            $log_helper->storeLog('edit', $model->no ?? $model->id, $this->subtitle);

            DB::commit();
            if ($request->ajax()) {
                $response           = [
                    'status'            => true,
                    'msg'               => 'Data Saved.',
                ];
                return response()->json($response);
            } else {
                return $this->redirectBackWithSuccess('Kasir berhasil ditutup');
            }
        } catch (Exception $e) {

            DB::rollback();
            if ($request->ajax()) {
                $response           = [
                    'status'            => false,
                    'msg'               => $e->getMessage(),
                ];
                return response()->json($response);
            } else {
                return $this->redirectBackWithError($e->getMessage());
            }
        }
    }
}
