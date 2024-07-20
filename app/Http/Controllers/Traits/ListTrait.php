<?php

namespace App\Http\Controllers\Traits;

use App\Models\Master\Addon;
use App\Models\Master\Package;
use App\Models\Transaction;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

trait ListTrait
{

    public function list_role()
    {
        return Role::pluck('name', 'id');
    }

    public function listPackage()
    {
        return Package::where('status', 1)->pluck('name', 'id');
    }

    public function listPackageData()
    {
        return Package::where('status', 1)->get();
    }

    public function listAddon()
    {
        return Addon::where('status', 1)->pluck('name', 'id');
    }

    public function listAddonData()
    {
        return Addon::where('status', 1)->get();
    }

    public function listCustomerToday()
    {
        return Transaction::where('status', 'open')->where('tanggal', date('Y-m-d'))->orderBy('id', 'desc')->pluck('customer_name', 'id');
    }

    public function listTransProcessToday()
    {
        return Transaction::whereIn('status', ['ordered', 'payment'])->where('tanggal', date('Y-m-d'))->orderBy('id', 'desc')->get();
    }
}
