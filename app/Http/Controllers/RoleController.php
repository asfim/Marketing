<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_roleObj;

    public function __construct()
    {
        $this->_roleObj     = new Role();
    }

    public function index()
    {
        $roles     = $this->_roleObj->where('name', '!=', 'super-admin')->orderBy('id')->get();
        return view('admin.role.role_view', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     $permissions    = Permission::orderBy('display_name')->get();
    //     // dd($permissions);
    //     return view('admin.role.role_add', compact('permissions'));
    // }

    public function create()
    {
        $permissions = Permission::orderBy('display_name')
            ->get()
            ->reject(function ($permission) {
                return str_contains($permission->display_name, 'Bank') || str_contains($permission->display_name, 'User');
            });

        return view('admin.role.role_add', compact('permissions'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules  = [
            'name'      => 'required|unique:roles'
        ];

        $this->validate($request, $rules);

        //$inputs     = $request->all();

        $role_save  = $this->_roleObj->create($request->except('permission', '_token'));
        if ($role_save->save()) {
            if (!empty($request->permission)) {
                foreach ($request->permission as $key => $value) {
                    $role_save->attachPermission($value);
                }
            }
            Session::flash('message', 'Data save successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('role.index');
        } else {
            Session::flash('message', 'Data save failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role_data  = $this->_roleObj->find($id);

        //Get the permissions linked to the role
        $permissions = Permission::join("permission_role", "permission_role.permission_id", "=", "permissions.id")
            ->where("permission_role.role_id", $id)
            ->orderBy('display_name', 'ASC')
            ->get();
        //return the view with the role info and its permissions
        return view('admin.role.role_show', compact('role_data', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role_data  = $this->_roleObj->find($id);
        $permissions    = Permission::orderBy('display_name', 'ASC')->get();
        $role_permissions   = $role_data->perms()->pluck('id', 'id')->toArray();

        return view('admin.role.role_edit', compact('role_data', 'permissions', 'role_permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules  = [
            'name'          => 'required|unique:roles,name,' . $id,
            'permission'    => 'required',
        ];

        $this->validate($request, $rules);

        //$inputs     = $request->all();

        $role_data  = $this->_roleObj->find($id);
        $role_update  = $role_data->update($request->except('permission', '_token'));
        if ($role_update == true) {
            //delete all permissions currently linked to this role
            DB::table("permission_role")->where("role_id", $id)->delete();

            foreach ($request->permission as $key => $value) {
                $role_data->attachPermission($value);
            }
            Session::flash('message', 'Data Update successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('role.index');
        } else {
            Session::flash('message', 'Data Update failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role_delete    = $this->_roleObj->where('id', $id)->delete();
        if ($role_delete == true) {
            Session::flash('message', 'Data Delete successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('role.index');
        } else {
            Session::flash('message', 'Data Delete failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }
}
