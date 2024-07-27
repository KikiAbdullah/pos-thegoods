<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OperatorController extends Controller
{

    public function __construct(Transaction $model, LogHelper $logHelper)
    {
        $this->title            = 'Operator';
        $this->subtitle         = 'Operator';
        $this->model_request    = Request::class;
        $this->folder           = '';
        $this->relation         = ['packages', 'addons'];
        $this->model            = $model;
        $this->logHelper        = $logHelper;
        $this->withTrashed      = false;
    }

    public function formData()
    {
        return [
            'kasir' => $this->kasirOpenToday(),
        ];
    }

    public function menuoption(Request $request)
    {
        $data         = $request->all();

        $trans      = $this->model->find($data['id']);

        $button     = [];


        switch ($trans->status) {
            case 'ordered':
                $button['payment']      = ('transaction.change-status');
                $button['upload-url-form']      = ('transaction.upload-url-form');
                break;
        }

        $view         = [
            'status'        => true,
            'view'          => view('operator.menuoption')->with(['id' => $data['id'], 'button' => $button])->render(),
        ];

        return response()->json($view);
    }
}
