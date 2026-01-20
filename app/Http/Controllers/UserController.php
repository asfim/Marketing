<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $_userObj,
        $_roleObj,
        $_branchObj;

    public function __construct()
    {
        $this->_userObj             = new User();
        $this->_roleObj             = new Role();
        $this->_branchObj           = new Branch();
    }

    public function index()
    {

        $roles     = $this->_roleObj->where('id', '!=', 1)->where('name', '!=', 'super-admin')->orderBy('id')->pluck('display_name', 'id');
        // dd($roles);
        $branches  = $this->_branchObj->orderBy('id')->pluck('name', 'id');
        $users     = $this->_userObj->orderBy('id')->get();

        return view('admin.pages.user_view', compact('users', 'roles', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->_roleObj->where('name', '!=', 'super-admin')->orderBy('display_name')->pluck('display_name', 'id');
        $branches = $this->_branchObj->orderBy('name')->pluck('name', 'id');

        return view('admin.pages.user_add', compact('roles', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required',
            'email'         => 'required|unique:users',
            'password'      => 'required|string|min:6|confirmed',
            'role'              => 'required',
        ];

        $this->validate($request, $rules);
        $inputs = $request->only('name', 'email', 'password');
        $inputs['password']         = bcrypt($request->input('password'));
        $inputs['branchId']         = $request->input('branch');

        $user_save  = $this->_userObj->create($inputs);
        $user_save->attachRole($request->input('role'));

        if ($user_save->save()) {
            Session::flash('message', 'Data save successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('user.index');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::where('name', '!=', 'super-admin')->get();
        $branches = Branch::all();
        return view('admin.pages.user_edit', compact('user', 'roles', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {

    //     dd($request->all());

    //     $rules = [
    //         'name'          => 'required',
    //         'email'         => 'required|unique:users,email,'.$id.',id',
    //         'password'          => 'string|min:6|confirmed',
    //         'role'              => 'required',
    //     ];

    //     $this->validate($request, $rules);
    //     $inputs = $request->only('name', 'email', 'password');
    //     if(!empty($inputs['password'])){
    //         $inputs['password'] = bcrypt($request->input('password'));
    //     }else{
    //         $inputs = $request->only('name', 'email');
    //     }
    //     $inputs['branchId']           = $request->input('branch');

    //     $user_data  = $this->_userObj->find($id);
    //     $user_update  = $user_data->update($inputs);

    //     //delete all roles currently linked to this user
    //     DB::table('role_user')->where('user_id',$id)->delete();
    //     $user_data->attachRole($request->input('role'));

    //     if($user_update == true)
    //     {
    //         Session::flash('message', 'Data Update successfully!');
    //         Session::flash('m-class', 'alert-success');
    //         return redirect()->back();
    //     }else{
    //         Session::flash('message', 'Data Update failed!');
    //         Session::flash('m-class', 'alert-danger');
    //         return redirect()->back();
    //     }
    // }

    public function update(Request $request, $id)
    {
        $user_data = $this->_userObj->find($id);
        if (!$user_data) {
            return redirect()->back()->with([
                'message' => 'User not found!',
                'm-class' => 'alert-danger'
            ]);
        }

        $rules = [
            'name'     => 'required',
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required',
        ];

        $this->validate($request, $rules);

        // Collect inputs but exclude email (since it's not changeable)
        $inputs = $request->only('name');

        // Handle password update if requested
        if ($request->has('update_pass') && $request->input('update_pass') === 'on') {
            $inputs['password'] = bcrypt($request->input('password'));
        }

        // Handle branch update
        $inputs['branchId'] = $request->input('branch');

        // Update user
        $user_update = $user_data->update($inputs);

        // Delete all existing roles linked to this user and assign a new role
        DB::table('role_user')->where('user_id', $id)->delete();
        $user_data->roles()->sync([$request->input('role')]); // set new role

        $user = User::find($id);
        $user->role_id = $request->input('role');
        $user->save();

        if ($user_update) {
            return redirect()->back()->with([
                'message' => 'Data updated successfully!',
                'm-class' => 'alert-success'
            ]);
        } else {
            return redirect()->back()->with([
                'message' => 'Data update failed!',
                'm-class' => 'alert-danger'
            ]);
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
        $user_data  = $this->_userObj->find($id);
        $user_delete  = $user_data->delete();
        if ($user_delete == true) {
            Session::flash('message', 'Data Delete successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data Delete failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function secretLogin($id)
    {
        if (!session()->has('_secret_token')) {
            session()->put('_secret_token', encrypt(auth()->user()->id));
        } else {
            session()->forget('_secret_token');
        }
        $user = User::findOrFail(decrypt($id));
        auth()->login($user, true);
        return redirect()->route('admin.home');
    }
}
