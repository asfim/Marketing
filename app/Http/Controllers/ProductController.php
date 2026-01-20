<?php

namespace App\Http\Controllers;

use App\Models\MixDesign;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\ProductName;
use App\Models\ProductStock;
use App\Models\StockAdjustment;
use App\Models\ProductConsumption;

class ProductController extends Controller
{
    public function index()
    {
        $products = ProductName::orderBy('name')->get();
        return view('admin.product.view_product_list',['products'=>$products]);
    }

    public function saveProductName(Request $request)
    {
        $rules = [
            'name' => 'required|unique:product_names',
            'category' => 'required',
            'conversion_rate' => 'nullable|numeric'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();

        $product = new ProductName();
        $product->name= $request->name;
        $product->category = $request->category;
        $product->description = $request->description;
        if(($request->category == "Sand" || $request->category == "Stone") && $request->conversion_rate == "") {
            Session::flash('message', "Enter conversion rate for {$request->category}!");
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

        $product->conversion_rate = $request->conversion_rate;
        $product->unit_price = $request->unit_price??0;
        $product->user_id = $user_data->id;
        $status  = $product->save();

        if($status) {
            Session::flash('message', 'Added Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function editProductName(Request $request)
    {
        $rules = [
            'name' => 'required|unique:product_names,name,'.$request->id.',id',
            'category' => 'required',
            'conversion_rate' => 'nullable|numeric'
        ];

        $this->validate($request, $rules);
        $id = $request->id;
        $pro_names = ProductName::find($id);
        if(($request->category == "Sand" || $request->category == "Stone") && $request->conversion_rate == "")
        {
            Session::flash('message', "Enter conversion rate for {$request->category}!");
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

        $pro_names->conversion_rate = $request->conversion_rate;
        $pro_names->unit_price = $request->unit_price;
        $pro_names->name= $request->name;
        $pro_names->category = $request->category;
        $pro_names->description = $request->description;
        $status  = $pro_names->save();

        if($status)
        {
            Session::flash('message', 'Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Updating Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function deleteProductName($id)
    {
        $tbl_product = ProductName::find($id);
        $tbl_product->delete();
        Session::flash('message', 'Deleted Successfully!');
        Session::flash('m-class', 'alert-success');
        return redirect()->back();
    }


    public function viewProductStock()
    {
        $uncheck_challans = ProductSale::where('status',1)->get();
        $stock_consumptionable = [];

        foreach($uncheck_challans as $challan){
            $mix_design = MixDesign::find($challan->mix_design_id);

            //STONE CONSUMPTION
            $a = 0;
            $stone_id_array = array_filter(explode(',', $mix_design->stone_id));
            $stone_qty_array = array_filter(explode(',', $mix_design->stone_quantity));
            foreach ($stone_id_array as $stone_id) {
                //convert stone kg to cft
                $conversion_rate = ProductName::where('id', $stone_id)->value('conversion_rate');
                if($conversion_rate)
                    $consumption_qty = $stone_qty_array[$a] * $challan->cuM / $conversion_rate;
                else
                    $consumption_qty = $stone_qty_array[$a] * $challan->cuM;

                if(array_key_exists($stone_id,$stock_consumptionable))
                    $stock_consumptionable[$stone_id] += $consumption_qty;
                else
                    $stock_consumptionable[$stone_id] = $consumption_qty;

                $a++;
            }

            //SAND CONSUMPTION
            $sand_id_array = array_filter(explode(',', $mix_design->sand_id));
            $sand_qty_array = array_filter(explode(',', $mix_design->sand_quantity));
            $b = 0;
            foreach ($sand_id_array as $sand_id) {
                //CONVERT SAND KG TO CFT
                $conversion_rate = ProductName::where('id', $sand_id)->value('conversion_rate');
                if($conversion_rate)
                    $consumption_qty = $sand_qty_array[$b] * $challan->cuM / $conversion_rate;
                else
                    $consumption_qty = $sand_qty_array[$b] * $challan->cuM;

                if(array_key_exists($sand_id,$stock_consumptionable))
                    $stock_consumptionable[$sand_id] += $consumption_qty;
                else
                    $stock_consumptionable[$sand_id] = $consumption_qty;


                $b++;
            }

            //CEMENT CONSUMPTION
            $cement_id_array = array_filter(explode(',', $mix_design->cement_id));
            $cement_qty_array = array_filter(explode(',', $mix_design->cement_quantity));
            $d = 0;
            foreach ($cement_id_array as $cement_id) {
                $consumption_qty = $cement_qty_array[$d] * $challan->cuM;

                if(array_key_exists($cement_id,$stock_consumptionable))
                    $stock_consumptionable[$cement_id] += $consumption_qty;
                else
                    $stock_consumptionable[$cement_id] = $consumption_qty;


                $d++;
            }


            //CHEMICAL CONSUMPTION
            $chemical_id_array = array_filter(explode(',', $mix_design->chemical_id));
            $chemical_qty_array = array_filter(explode(',', $mix_design->chemical_quantity));
            $c = 0;
            foreach ($chemical_id_array as $chemical_id) {
                $consumption_qty = $chemical_qty_array[$c] * $challan->cuM;

                if(array_key_exists($chemical_id,$stock_consumptionable))
                    $stock_consumptionable[$chemical_id] += $consumption_qty;
                else
                    $stock_consumptionable[$chemical_id] = $consumption_qty;

                $c++;
            }


        }

        $stocks = ProductStock::get();
        return view('admin.product.view_product_stock',compact('stocks','stock_consumptionable'));
    }

    public function updateProductStock(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'quantity' => 'required|numeric'
        ];

        $this->validate($request, $rules);

        $stock = ProductStock::findOrFail($request->id);
        $stock->quantity = $request->quantity;
        $status = $stock->save();

        if($status)
        {
            Session::flash('message', 'Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Updating Data Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }


    public function viewStockAdjustmentForm()
    {
        $product_names = ProductName::all();
        return view('admin.product.add-stock-adjust',['product_names'=>$product_names]);
    }

    public function saveStockAdjustment(Request $request)
    {
        $rules = [
            'adjustment_date' => 'required',
            'product_id' => 'required|numeric',
            'adjustment_qty' => 'required|numeric',
            'unit_type' => 'required'
        ];

        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            $stock_adj = new StockAdjustment();
            $stock_adj->adjustment_date = date('Y-m-d', strtotime($request->adjustment_date));
            $stock_adj->product_id = $request->product_id;
            $stock_adj->adjustment_qty = $request->adjustment_qty;
            $stock_adj->unit_type = $request->unit_type;
            $stock_adj->description = $request->description;
            $stock_adj->save();

            $product_stock = ProductStock::where('product_name_id', $request->product_id)->first();
            if ($product_stock->quantity < 0) {
                $current_qty = $product_stock->quantity + $request->adjustment_qty;
            } else {
                $current_qty = $product_stock->quantity - $request->adjustment_qty;
            }

            $product_stock->update(['quantity' => $current_qty]);

            DB::commit();
            Session::flash('message', 'Adjust Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch(\Exception $e){
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function viewStockAdjust(Request $request)
    {
        $product_names = ProductName::all();
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } elseif ($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $stockAdj  = new StockAdjustment();

        if(isset($date_range)) {
            $stockAdj = $stockAdj->whereBetween('adjustment_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($request->search_text != '') {
            $stockAdj = $stockAdj->where('adjustment_qty', 'LIKE', '%' . $request->search_text . '%')
                ->orwhere('unit_type', $request->search_text)
                ->orWhere('description', 'LIKE', '%' . $request->search_text . '%')
                ->orWhereHas('product_name', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search_text . '%');
                });
        }

        $stockAdj = $stockAdj->orderBy('id','DESC')->get();

        return view('admin.product.view_stock_adjust',  compact('stockAdj','product_names'));
    }

    public function viewProductConsumption(Request $request)
    {
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } elseif ($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $prod_consump  = new ProductConsumption();

        if(isset($date_range)) {
            $prod_consump = $prod_consump->whereBetween('consumption_date', [
                \Carbon\Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($request->search_text != '') {
            $prod_consump = $prod_consump->where('consumption_qty', 'LIKE', '%'.$request->search_text.'%')
                ->orwhere('unit_type', $request->search_text)
                ->orWhere('transaction_id', 'LIKE', '%'.$request->search_text.'%')
                ->orWhereHas('product_names', function($q) use ($request){
                    $q->where('name', 'LIKE', '%'.$request->search_text.'%');
                });
        }

        $prod_consump = $prod_consump->orderBy('id','DESC')->get();

        return view('admin.product.view_product_consumption',  compact('prod_consump'));
    }
}
