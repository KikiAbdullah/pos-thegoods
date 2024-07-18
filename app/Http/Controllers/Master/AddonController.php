<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Addon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AddonController extends Controller
{
    public function __construct(Addon $model)
    {
        $this->title            = 'Addon';
        $this->subtitle         = 'Addon List';
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
            ->editColumn('harga', function ($data) {
                return cleanNumber($data->harga);
            })
            ->addColumn('status_formatted', function ($data) {
                if ($data->status == 1) {
                    return "<div class=\"badge bg-primary\">Show</div>";
                } else {
                    return "<div class=\"badge bg-light text-body\">Hidden</div>";
                }
            })
            ->rawColumns(['status_formatted'])
            ->toJson();
    }
}
