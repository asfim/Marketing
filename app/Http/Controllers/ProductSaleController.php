<?php

namespace App\Http\Controllers;

use App\Models\DemoProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ProductName;
use App\Models\Customer;
use App\Models\MixDesign;
use App\Models\ProductSale;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class ProductSaleController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->branchId??$user->branchId;
        if($request->filled("date_range")){
            $date_range = date_range_to_arr($request->date_range);
        }
        elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }
        $challans  = new ProductSale();

        if($branchId == 'head_office'){
            $challans = $challans->where('branchId', null);
        } elseif ($branchId != ''){
            $challans = $challans->where('branchId', $branchId);
        }

        if(isset($date_range)) {
            $challans = $challans->whereBetween('sell_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($request->challan_status != '') {
            $challans = $challans->where('status', $request->challan_status);
        }

        if($request->search_text) {
            $challans = $challans->where(function($query) use ($request){
                $query->where('challan_no', $request->search_text)
                    ->orWhereHas('customer', function($q) use ($request){
                        $q->where('name', 'LIKE', '%'.$request->search_text.'%');
                    });
            });
        }

        $challans = $challans->orderBy('sell_date','DESC')->get();

        $customers = Customer::orderBy('name','ASC')->get();
        $checkbox_enable = 0;
        $custprojects = DB::table('customer_projects')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.product.view_challan_list',compact('branches','challans', 'customers','checkbox_enable', 'custprojects'));
    }

    public function create()
    {
        $user_data = Auth::user();
        $customers = Customer::orderBy('name','ASC')->get();
        if ($user_data->branchId) {
            $branches = Branch::where('id', $user_data->branchId)->get();
        } else {
            $branches = Branch::all();
        }
        return view('admin.product.add_challan', compact('customers','branches','user_data'));
    }


    public function loadSellProductInfo(Request $request)
    {
        $customer = Customer::with(['projects', 'mixDesigns'])->find($request->customer_id);

        if (!$customer) {
            return response()->json(['projects' => [], 'psi' => []]);
        }

        return response()->json([
            'projects' => $customer->projects->map(function ($p) {
                return ['id' => $p->id, 'name' => $p->name];
            }),
            'psi' => $customer->mixDesigns->map(function ($m) {
                return ['id' => $m->id, 'psi' => $m->psi];
            })
        ]);
    }


    public function store(Request $request)
    {
        $rules = [
            'customer_id' => 'required|numeric',
            'challan_no' => 'required|unique:product_sales',
            'project_id' => 'required|numeric',
            'sell_date' => 'required',
            'mix_design_id' => 'required',
            'cuM' => 'required',
            'branchId' => 'nullable|numeric'
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $sell_date = date('Y-m-d',strtotime($request->sell_date));

        //concrete no auto increment
        $max_concrete = ProductSale::max('concrete_no');
        if($max_concrete == "") $max_concrete = 1; else $max_concrete++;

        $chalan = new ProductSale();
        $chalan->challan_no = $request->challan_no;
        $chalan->customer_id = $request->customer_id;
        $chalan->mix_design_id = $request->mix_design_id;
        $chalan->project_id = $request->project_id;
        $chalan->cuM = $request->cuM;
        $chalan->sell_date = $sell_date;
        $chalan->concrete_no = $max_concrete;
        $chalan->description = $request->description;
        $chalan->user_id = $user_data->id;
        $chalan->branchId = $request->branchId;
        $chalan->status = 1;




        $status  = $chalan->save();





        $demochalan = new DemoProductSale();
        $demochalan->challan_no = $request->challan_no;
        $demochalan->customer_id = $request->customer_id;
        $demochalan->mix_design_id = $request->mix_design_id;
        $demochalan->project_id = $request->project_id;
        $demochalan->cuM = $request->cuM;
        $demochalan->sell_date = $sell_date;
        $demochalan->concrete_no = $max_concrete;
        $demochalan->description = $request->description;
        $demochalan->user_id = $user_data->id;
        $demochalan->branchId = $request->branchId;
        $demochalan->status = 1;
        $demochalan->save();


        if($status) {
            Session::flash('message', 'Challan Added Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data Save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'project_id' => 'required',
            'mix_design_id' => 'required',
            'sell_date' => 'required',
            'cuM' => 'required',
            'rate' => 'required|numeric', //validation for rate
        ];
        $this->validate($request, $rules);

        $sell_date = date('Y-m-d',strtotime($request->sell_date));
        $challan = ProductSale::find($request->id);

        if($challan->status == 0){
            Session::flash('message', 'Cann\'t edit! Bill already generated against this challan');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

        $challan->customer_id = $request->customer_id;
        $challan->project_id = $request->project_id;
        $challan->mix_design_id = $request->mix_design_id;
        $challan->sell_date = $sell_date;
        $challan->cuM = $request->cuM;
        $challan->rate = $request->rate; //  store rate
        $status = $challan->save();



        $demochallan = DemoProductSale::find($request->id);
        if($demochallan->status == 0){
            Session::flash('message', 'Cann\'t edit! Bill already generated against this challan');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
        $demochallan->customer_id = $request->customer_id;
        $demochallan->project_id = $request->project_id;
        $demochallan->mix_design_id = $request->mix_design_id;
        $demochallan->sell_date = $sell_date;
        $demochallan->cuM = $request->cuM;
        $demochallan->rate = $request->rate; // store rate
        $demochallan->save();


        if($status) {
            Session::flash('message', 'Challan Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data Save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $challan = ProductSale::findOrFail($id);


        if($challan->status == 0){
            Session::flash('message', 'Cann\'t delete! Bill already generated against this challan');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }


        if($challan->delete()) {

            $demochallan = DemoProductSale::findOrFail($id);
            $demochallan->delete();
            Session::flash('message', 'Challan Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        }

        else {
            Session::flash('message', 'Data Save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function checkChallanList(Request $request)
    {
        $customers = Customer::orderBy('name','ASC')->get();
        $cust_id = $request->customer_id;
        $psi = $request->psi;
        $project_id = $request->project;
        $checkbox_enable = "";
        $challans = "";

        if($cust_id != "" && $psi != "" && $project_id != "")
        {
            $challans = ProductSale::where('customer_id',$cust_id)
                ->where('psi','=',$psi)
                ->where('project_id',$project_id)
                ->where('status',1)
                ->get();
            $checkbox_enable = 1;
            $request = "";
        }

        else {
            //$challans = ProductSale::orderBy('id','DESC')->get();
            $today = date('Y-m-d');
            $last_month = date('Y-m-1', strtotime('this month'));
            $challans = ProductSale::whereBetween('sell_date',[$last_month,$today])->orderBy('sell_date','DESC')->paginate(500);
            $grant_total_qty = ProductSale::sum('cuM');
            $checkbox_enable = 0;
        }
        $custprojects = DB::table('customer_projects')->get();
        return view('admin.product.view_challan_list',compact('challans','customers','checkbox_enable', 'custprojects'));
    }



    public function viewMixDesignForm()
    {
        $customers = Customer::orderBy('name','ASC')->get();
        $cements = ProductName::where('category','Cement')->get();
        $sands = ProductName::where('category','Sand')->get();
        $stones = ProductName::where('category','Stone')->get();
        $chemicals = ProductName::where('category','Chemical')->get();
        return view('admin.product.add_mix_design',  compact('customers','cements','chemicals','sands','stones'));
    }

    public function saveMixDesign(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'psi' => 'required',
            'stone_id' => 'required',
            'stone_quantity' => 'required',
            'chemical_id' => 'required',
            'chemical_quantity' => 'required',
            'sand_id' => 'required',
            'sand_quantity' => 'required',
            'cement_id' => 'required',
            'cement_quantity' => 'required',
            'rate' => 'required'
        ];

        $this->validate($request, $rules);
        DB::beginTransaction();
        try {
            $stone_ids = implode(',', array_filter((array)$request->stone_id));
            $stone_quantity = implode(',', array_filter((array)$request->stone_quantity));
            $chemical_ids = implode(',', array_filter((array)$request->chemical_id));
            $chemical_quantity = implode(',', array_filter((array)$request->chemical_quantity));
            $sand_ids = implode(',', array_filter((array)$request->sand_id));
            $sand_quantity = implode(',', array_filter((array)$request->sand_quantity));
            $cement_quantity = implode(',', array_filter((array)$request->cement_quantity));
            $cement_ids = implode(',', array_filter((array)$request->cement_id));

            $user_data = Auth::user();

            $row_exists = MixDesign::where('customer_id', $request->customer_id)->where('psi', $request->psi)->first();

            if (!$row_exists) {
                $mix = new MixDesign();
                $mix->customer_id = $request->get('customer_id');
                $mix->psi = $request->psi;
                $mix->description = $request->description;
                $mix->rate = $request->rate;
                $mix->stone_id = $stone_ids;
                $mix->stone_quantity = $stone_quantity;
                $mix->sand_id = $sand_ids;
                $mix->sand_quantity = $sand_quantity;
                $mix->chemical_id = $chemical_ids;
                $mix->chemical_quantity = $chemical_quantity;
                $mix->cement_id = $cement_ids;
                $mix->cement_quantity = $cement_quantity;
                $mix->water = $request->water;
                $mix->water_quantity = $request->water_quantity??0;
                $mix->user_id = $user_data->id;
                $mix->save();

            } else {
                throw new \Exception('This Mix design Already exists for this customer');
            }

            DB::commit();
            Session::flash('message', 'Mix Design Added Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('customer.profile',$request->customer_id);
        } catch(\Exception $e){
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }



    }

    public function viewMixDesign($cust_id)
    {
        $designs = MixDesign::where('customer_id',$cust_id)->orderBy('psi','ASC')->get();

        $cust_name = Customer::where('id',$cust_id)->value('name');

        return view('admin.product.view_mix_design',  compact('designs','cust_name'));
    }

    public function editMixDesign($id)
    {
        $mix_design_row = MixDesign::where('id',$id)->first();
        $customer_name = Customer::where('id',$mix_design_row->customer_id)->value('name');
        $cements = ProductName::where('category','Cement')->get();
        $sands = ProductName::where('category','Sand')->get();
        $stones = ProductName::where('category','Stone')->get();
        $chemicals = ProductName::where('category','Chemical')->get();
        return view('admin.product.edit_mix_design',  compact('customer_name','cements','chemicals','sands','stones','mix_design_row'));
    }

    public function updateMixDesign(Request $request)
    {
        $rules = [
            'cust_id' => 'required',
            'id' => 'required',
            'rate' => 'required'
        ];
        $this->validate($request, $rules);

        DB::beginTransaction();
        try{
            $mix = MixDesign::find($request->id);

            $stone_ids = implode(',', (array)$request->stone_id);
            $stone_quantity = implode(',', (array)$request->stone_quantity);
            $chemical_ids = implode(',', (array)$request->chemical_id);
            $chemical_quantity = implode(',', (array)$request->chemical_quantity);
            $sand_ids = implode(',', (array)$request->sand_id);
            $sand_quantity = implode(',', (array)$request->sand_quantity);
            $cement_quantity = implode(',', (array)$request->cement_quantity);
            $cement_ids = implode(',', (array)$request->cement_id);

            $rate = $request->rate;
            $des = $request->description;

            if ($rate != "") $mix->rate = $rate;

            if ($stone_ids != "") $mix->stone_id = $stone_ids;

            if ($stone_quantity != "") $mix->stone_quantity = $stone_quantity;

            if ($sand_ids != "") $mix->sand_id = $sand_ids;

            if ($sand_quantity != "") $mix->sand_quantity = $sand_quantity;

            if ($chemical_ids != "") $mix->chemical_id = $chemical_ids;

            if ($chemical_quantity != "") $mix->chemical_quantity = $chemical_quantity;

            if ($cement_ids != "") $mix->cement_id = $cement_ids;

            if ($cement_quantity != "") $mix->cement_quantity = $cement_quantity;

            if ($request->water != "") $mix->water = $request->water;

            if ($request->water_quantity != "") $mix->water_quantity = $request->water_quantity;

            if ($des != "") $mix->description = $des;

            $mix->save();


            DB::commit();
            Session::flash('message', 'Mix Design Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('customer.profile',$request->cust_id);
        } catch(\Exception $e){
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

    }

    public function deleteMixDesign($id)
    {
        $mix_design = MixDesign::where('id',$id)->first();

        if($mix_design->challans()->exists()){
            Session::flash('message', 'Cann\'t delete! Challan record found with this Mix Design');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

        $mix_design->delete();

        Session::flash('message', 'Mix Design Deleted Successfully!');
        Session::flash('m-class', 'alert-success');
        return redirect()->back();
    }
}
