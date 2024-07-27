<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\TipePembayaran;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TipePembayaranController extends Controller
{
    public function __construct(TipePembayaran $model)
    {
        $this->title            = 'Tipe Pembayaran';
        $this->subtitle         = 'Tipe Pembayaran List';
        $this->model_request    = Request::class;
        $this->folder           = 'master';
        $this->relation         = [];
        $this->model            = $model;
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
            ->toJson();
    }
}
