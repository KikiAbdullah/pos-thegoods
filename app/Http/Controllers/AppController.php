<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;
use App\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use DB;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:edit_roles', ['only' => ['menuoptionroles']]);
    }

    public function index(Request $request)
    {
        $data['title']      = "Dashboard";
        $data['subtitle']   = "Hi, " . auth()->user()->name;

        $startDate          = Carbon::now()->subDays(30);
        $endDate            = Carbon::now();

        $transaksiBase      = Transaction::whereBetween('tanggal', [$startDate, $endDate]);

        $transAll           = $transaksiBase->count();
        $transBelumDilayani = (clone $transaksiBase)->where('status', 'open')->count();
        $transDilayani      = (clone $transaksiBase)->whereIn('status', ['ordered', 'payment'])->count();
        $transSelesai       = (clone $transaksiBase)->where('status', 'verify')->count();

        $data['total']      = [
            'all'               => $transAll,
            'open'              => $transBelumDilayani,
            'ordered'           => $transDilayani,
            'verify'            => $transSelesai,
        ];
        $data['userlog']    = UserLog::all();

        return view('home')->with($data);
    }

    public function permission()
    {
        $data['data'] = Permission::all();
        $data['title'] = "Permissions";
        $data['subtitle'] = "List of permissions";
        return view('user-setup.permission')->with($data);
    }

    public function role()
    {
        $data['title'] = "Roles";
        $data['subtitle'] = "Add or Remove Roles, Modify Permissions";
        return view('user-setup.role')->with($data);
    }

    public function getroles(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Role::query())->toJson();
        }
    }

    public function lineroles(Request $request)
    {
        $data['nama'] = $request->name;
        return view('user-setup.setrole')->with($data);
    }

    public function permissionlist()
    {
        $data['title'] = "Permission List";
        $data['subtitle'] = "Your current permission";
        $data['data'] = auth()->user()->getAllPermissions();
        return view('user-setup.permission')->with($data);
    }

    public function hakakses(Request $request)
    {
        if ($request->ajax()) {
            $permission = Role::findByName($request->name)->permissions;
            $data = Permission::all();
            return DataTables::of($data->diff($permission))
                ->addColumn('opt', '<center><a href="#!" class="text-muted" onclick="pilih(\'{{ $id }}\')"><i class="ph-arrow-right"></i></a></center>')
                ->rawColumns(['opt'])
                ->toJson();
        }
    }

    public function hakakses2(Request $request)
    {
        if ($request->ajax()) {
            $permission = Role::findByName($request->name)->permissions;
            return DataTables::of($permission)
                ->addColumn('opt', '<center><a href="#!" class="text-dark" onclick="pilih(\'{{ $id }}\')"><i class="ph-arrow-left"></i></a></center>')
                ->rawColumns(['opt'])
                ->toJson();
        }
    }

    public function menuoptionroles(Request $request)
    {
        // return '<a href="#!" onclick="hapus(\''.$request->name.'\')" class="btn btn-float has-text text-uppercase"><i class="icon-cross2 text-indigo"></i> <span>DELETE</span></a>';
        return '<a href="#!" onclick="hapus(\'' . $request->name . '\')" class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i class="ph-trash ph-2x text-danger"></i>DELETE</a>';
    }

    public function addhakakses(Request $request)
    {
        $role = Role::findByName($request->name);
        $role->givePermissionTo($request->namapilih);
    }

    public function removehakakses(Request $request)
    {
        $request->validate(['namapilih' => 'required']);
        $role = Role::findByName($request->name);
        $role->revokePermissionTo($request->namapilih);
    }

    public function deleteroles(Request $request)
    {
        try {
            if ($request->name == "SUPERADMIN") {
                return response()->json([
                    'status' => false,
                    'msg' => 'Can\'t delete SUPERADMIN :)',
                ]);
            }
            $role = Role::findByName($request->name);
            $role->delete();
            return response()->json([
                'status' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. ' . $e->getMessage(),
            ]);
        }
    }

    public function saverole(Request $request)
    {
        try {
            if (!empty($request->name)) {
                if (!Role::where('name', $request->name)->get()->isNotEmpty()) {
                    DB::beginTransaction();
                    Role::create(['name' => strtoupper($request->name)]);
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'msg' => 'Role added Successfully'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Role Name already exist.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Role Name can\'t empty.',
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. ' . $e->getMessage(),
            ]);
        }
    }

    public function toggletheme()
    {
        $value = Cookie::get('nhTheme');
        if (empty($value)) {
            $lifetime = time() + 60 * 60 * 24 * 365;
            Cookie::queue('nhTheme', '1', $lifetime);
        } else {
            Cookie::queue(Cookie::forget('nhTheme'));
        }
        return back();
    }

    public function changepassword(Request $request)
    {
        if ($request->newpass <> "") {
            $model = User::where('id', auth()->user()->id)->where('password', md5($request->current))->get();
            if ($model->isNotEmpty()) {
                User::where('id', auth()->user()->id)->update(['password' => md5($request->newpass)]);
                return response()->json([
                    'status' => true,
                    'msg' => 'Password Saved Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Current User & Password didn\'t match',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'New Password can\'t empty.',
            ]);
        }
    }
}
