<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Auth;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $_branchObj;

    public function __construct()
    {
        $this->_branchObj   = new Branch();
    }

    public function index()
    {
        $user   = Auth::user();
        $branches      = $this->_branchObj->orderBy('id', 'ASC')->get();

        return view('admin.pages.branch_view', compact('branches','user'));
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
            'name'    => 'required|unique:branches|max:100'
        ];

        $this->validate($request, $rules);

        $inputs = $request->all();

        $branch_save    = $this->_branchObj->create($inputs);




        if($branch_save->save()){

            if ($request->has('as_main_branch') && $request->has('as_main_branch')){
                Branch::query()->update(['is_main_branch' => 0]);
                Branch::where('id',$branch_save->id)->update(['is_main_branch' => 1]);

            }
            Session::flash('message', 'Data Save Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('branches.index');
        }else{
            Session::flash('message', 'Data Save Failed!');
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
        //
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
            'name'    => 'required|unique:branches,name,'.$id.',id|max:100'
        ];

        $this->validate($request, $rules);

        $inputs = $request->all();

        $branch_data    = $this->_branchObj->findOrFail($id);
        $branch_update    = $branch_data->update($inputs);
        if($branch_update == true){

            if ($request->has('as_main_branch') && $request->has('as_main_branch')){
                Branch::query()->update(['is_main_branch' => 0]);
                Branch::where('id',$branch_data->id)->update(['is_main_branch' => 1]);

            }
            Session::flash('message', 'Data Update Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('branches.index');
        }else{
            Session::flash('message', 'Data Update Failed!');
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
        $branch_data    = $this->_branchObj->findOrFail($id);
        $branch_delete    = $branch_data->delete();
        if($branch_delete == true){
            Session::flash('message', 'Data Delete Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('branches.index');
        }else{
            Session::flash('message', 'Data Delete Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }
}
