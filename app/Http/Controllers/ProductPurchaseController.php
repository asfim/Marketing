<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashStatement;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\ProductName;
use App\Models\ProductPurchase;
use App\Models\ProductStock;
use App\Models\Supplier;
use App\Models\SupplierStatement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProductPurchaseController extends Controller
{

//    


public function productPurchaseList(Request $request)

{
  $user_data = Auth::user();

        // 1. Handle date range
        if ($request->filled("date_range")) {
            $date_range = date_range_to_arr($request->date_range);
        } else {
            if ($request->has("date_range") && !$request->filled("date_range")) {
                $date_range = null;
            } else {
                $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
            }
        }

        // 2. Initialize queries with relationships
        $check_p = ProductPurchase::select(DB::raw('bill_no, count(*) as tor,
            MAX(received_date) as received_date,
            MAX(dmr_no) as dmr_no,
            MAX(check_status) as check_status,
            MAX(chalan_no) as chalan_no,
            MAX(supplier_id) as supplier_id,
            MAX(product_name_id) as product_name_id,
            MAX(branchId) as branchId,
            SUM(material_cost) as material_cost,
            SUM(total_material_cost) as total_material_cost,
            SUM(truck_rent) as truck_rent,
            SUM(unload_bill) as unload_bill,
            SUM(product_qty) as product_qty'))
            ->with(['supplier', 'product_name', 'branch']);

        $purchases = ProductPurchase::with(['supplier', 'product_name', 'branch']);

        // 3. Branch filtering for non-admin users
        $admin_ids = [1, 16, 17, 18, 19, 21, 22, 23];
        if (!in_array($user_data->id, $admin_ids)) {
            $purchases = $purchases->where('branchId', $user_data->branchId);
            $check_p = $check_p->where('branchId', $user_data->branchId);
        }

        // 4. Branch filter for admin users (from request)
        if ($request->filled('branch_filter') && in_array($user_data->id, $admin_ids)) {
            $branch_filter = $request->branch_filter;
            $purchases = $purchases->where('branchId', $branch_filter);
            $check_p = $check_p->where('branchId', $branch_filter);
        }

        // 5. Search functionality
        $hasSearch = $request->filled('search_text') || $request->filled('search_text2');

        if ($hasSearch) {
            if ($request->filled('search_text')) {
                $search_text = $request->search_text;

                $purchases = $purchases->where(function ($query) use ($search_text) {
                    $query->where('dmr_no', '=', $search_text)
                        ->orWhere('chalan_no', '=', $search_text)
                        ->orWhere('product_qty', '=', $search_text)
                        ->orWhere('rate_per_unit', '=', $search_text)
                        ->orWhere('material_cost', '=', $search_text)
                        ->orWhere('total_material_cost', '=', $search_text)
                        ->orWhereHas('supplier', function ($q) use ($search_text) {
                            $q->where('name', 'like', '%' . $search_text . '%');
                        })
                        ->orWhereHas('product_name', function ($q) use ($search_text) {
                            $q->where('name', 'like', '%' . $search_text . '%');
                        })
                        ->orWhereHas('branch', function ($q) use ($search_text) {
                            $q->where('name', 'like', '%' . $search_text . '%');
                        });
                });

                $check_p = $check_p->where(function ($query) use ($search_text) {
                    $query->where('dmr_no', '=', $search_text)
                        ->orWhere('chalan_no', '=', $search_text)
                        ->orWhere('product_qty', '=', $search_text)
                        ->orWhere('rate_per_unit', '=', $search_text)
                        ->orWhere('material_cost', '=', $search_text)
                        ->orWhere('total_material_cost', '=', $search_text)
                        ->orWhereHas('supplier', function ($q) use ($search_text) {
                            $q->where('name', 'like', '%' . $search_text . '%');
                        })
                        ->orWhereHas('product_name', function ($q) use ($search_text) {
                            $q->where('name', 'like', '%' . $search_text . '%');
                        })
                        ->orWhereHas('branch', function ($q) use ($search_text) {
                            $q->where('name', 'like', '%' . $search_text . '%');
                        });
                });
            }

            if ($request->filled('search_text2')) {
                $search_text2 = $request->search_text2;

                $purchases = $purchases->whereHas('product_name', function ($q) use ($search_text2) {
                    $q->where('name', 'like', '%' . $search_text2 . '%');
                });

                $check_p = $check_p->whereHas('product_name', function ($q) use ($search_text2) {
                    $q->where('name', 'like', '%' . $search_text2 . '%');
                });
            }
        }

        // 6. Date range filtering
        if (isset($date_range)) {
            $purchases = $purchases->whereBetween('received_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);

            $check_p = $check_p->whereBetween('received_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        // 7. Fetch unchecked purchases
        $purchases = $purchases->where('check_status', 0)
            ->orderBy('check_status', 'ASC')
            ->orderBy('received_date', 'DESC')
            ->get();

        // 8. Fetch checked purchases (grouped by bill)
        $check_p = $check_p->where('check_status', 1)
            ->orderBy('received_date', 'DESC')
            ->groupBy('bill_no')
            ->get();

        // 9. Calculate grant total quantity
        $grant_total_qty = ProductPurchase::sum('product_qty') - SupplierStatement::sum('adjustment_qty');

        // 10. Fetch additional data
        $product_names = ProductName::all();
        $suppliers = Supplier::orderBy('name', 'ASC')->get();
        $branches = Branch::all();

        // 11. Calculate branch-wise totals
        $branch_totals_query = ProductPurchase::where('check_status', 0);
        
        if (!in_array($user_data->id, $admin_ids)) {
            $branch_totals_query = $branch_totals_query->where('branchId', $user_data->branchId);
        }
        
        if ($request->filled('branch_filter') && in_array($user_data->id, $admin_ids)) {
            $branch_totals_query = $branch_totals_query->where('branchId', $request->branch_filter);
        }
        
        if (isset($date_range)) {
            $branch_totals_query = $branch_totals_query->whereBetween('received_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }
        
        $branch_totals = $branch_totals_query->select('branchId',
                DB::raw('SUM(product_qty) as total_qty'),
                DB::raw('SUM(total_material_cost) as total_cost'))
            ->groupBy('branchId')
            ->with('branch')
            ->get();

        // 12. Return view with all data
        return view('admin.product.view_product_purchase_list', compact(
            'purchases',
            'check_p',
            'product_names',
            'suppliers',
            'grant_total_qty',
            'branches',
            'branch_totals',
            'user_data',
            'admin_ids'
        ));
    }


    public function productPurchaseForm()
    {
        $suppliers = Supplier::orderBy('name', 'ASC')->get();
        $branches = Branch::all();

        $products = ProductName::all();
        return view('admin.product.add_product_purchase', compact('suppliers', 'products', 'branches'));
    }

    public function editProductPurchase(Request $request, $id)
    {
        $purchase = ProductPurchase::findOrFail($id);
        $suppliers = Supplier::orderBy('name', 'ASC')->get();
        $branches = Branch::all();

        $products = ProductName::all();
        return view('admin.product.edit_product_purchase', compact('purchase', 'suppliers', 'products', 'branches'));
    }

    public function resetForm()
    {
        session()->forget('form_input'); // clear previous session values
        return redirect()->back();
    }

    public function saveProductPurchase(Request $request)
    {

        $rules = [
            'dmr_no' => 'required|unique:product_purchases',
            'chalan_no' => 'required',
            'supplier_id' => 'required|numeric',
            'product_name_id' => 'required|numeric',
            'received_date' => 'required',
            'unit_type' => 'required',
            'product_qty' => 'required|numeric',
            'rate_per_unit' => 'required|numeric',
            'material_cost' => 'required|numeric',
            'total_material_cost' => 'required|numeric',
            'branchId' => 'nullable|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        session(['form_input' => $request->all()]);

        ///VALIDATE UNIT TYPE
        $product = ProductName::find($request->product_name_id);

        DB::beginTransaction();
        try {
            if ($product->category == 'Stone' && $request->unit_type == 'KG')
                throw new \Exception("Select unit type CFT or Ton for material type Stone!");
            if ($product->category == 'Sand' && $request->unit_type != 'CFT')
                throw new \Exception("Select unit type CFT for material type Sand!");
            if (($product->category == 'Cement' || $product->category == 'Chemical') && $request->unit_type == 'CFT')
                throw new \Exception("Select unit type KG or Ton for material type {$product->category}!");

            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    //$file_name = 'product-'.time().'.'.$ext;
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;

                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/');
                    } else {
                        $destinationPath = '/img/files/';
                    }
                    $year_folder = $destinationPath . date("Y") . "/";
                    $month_folder = $year_folder . date("m") . "/";
                    $day_folder = $month_folder . date("d") . "/";

                    !file_exists($year_folder) && mkdir($year_folder, 0777);
                    !file_exists($month_folder) && mkdir($month_folder, 0777);
                    !file_exists($day_folder) && mkdir($day_folder, 0777);

                    $file->move($day_folder, $file_name);
                    $file_names[] = $file_name;
                }
            }
            $com_file_names = implode(',', $file_names);

            //description
            $description = "Mat. Name: " . $product->name . ", " . $request->description;
            $received_date = date('Y-m-d', strtotime($request->received_date));

            $pp_no = ProductPurchase::max('id') + 1;
            $product_purchase = new ProductPurchase();
            $product_purchase->transaction_id = 'PP-' . $pp_no;
            $product_purchase->dmr_no = $request->dmr_no;
            $product_purchase->chalan_no = $request->chalan_no;
            $product_purchase->supplier_id = $request->supplier_id;
            $product_purchase->vehicle_no = $request->vehicle_no;
            $product_purchase->product_name_id = $request->product_name_id;
            $product_purchase->received_date = $received_date;
            $product_purchase->purchase_date = date('Y-m-d');
            $product_purchase->unit_type = $request->unit_type;
            $product_purchase->product_qty = $request->product_qty;
            $product_purchase->rate_per_unit = $request->rate_per_unit;
            $product_purchase->material_cost = $request->material_cost;
            $product_purchase->truck_rent = $request->truck_rent ?? 0;
            $product_purchase->unload_bill = $request->unload_bill ?? 0;
            $product_purchase->total_material_cost = $request->total_material_cost;
            $product_purchase->branchId = $request->branchId;
            $product_purchase->file = $com_file_names;
            $product_purchase->description = $request->description;
            $product_purchase->user_id = $user_data->id;
            $status = $product_purchase->save();
//dd('202');
            //SAVE QUANTITY IN PRODUCT STOCK TABLE FOR FIRST TIME
            $stock_exist = ProductStock::where('product_name_id', $request->product_name_id)->first();
            if (!$stock_exist) {
                $product_stock = new ProductStock();
                $product_stock->consumption_qty = 0;
                $product_stock->product_name_id = $request->product_name_id;
                //NOTE: Stone purchase in Ton/CFT but stored in cft
                if ($product->category == 'Stone' && $request->unit_type == 'Ton') {
                    $product_stock->quantity = $request->product_qty * 40;
                    $product_stock->unit_type = 'CFT';
                } else if ($request->unit_type == 'Ton') {
                    $product_stock->quantity = $request->product_qty * 1000;
                    $product_stock->unit_type = 'KG';
                } else {
                    $product_stock->quantity = $request->product_qty;
                    $product_stock->unit_type = $request->unit_type;
                }
                $product_stock->save();
            } else {
                $quantity = $stock_exist->quantity;
                $data = array();
                if ($product->category == 'Stone' && $request->unit_type == 'Ton') {
                    $data['quantity'] = $request->product_qty * 40 + $quantity;
                } else if ($request->unit_type == 'Ton') {
                    $data['quantity'] = $request->product_qty * 1000 + $quantity;
                } else {
                    $data['quantity'] = $request->product_qty + $quantity;
                }

                $stock_exist->update($data);
            }

            //SAVE SUPPLIER STATEMENT
            $supplier = Supplier::find($request->supplier_id);
            $current_bal = $supplier->balance() + $request->material_cost;
            $s_statement = new SupplierStatement();
            $s_statement->transaction_id = 'PP-' . $pp_no;
            $s_statement->posting_date = $received_date;
            $s_statement->description = $description;
            $s_statement->table_name = 'product_purchases';
            $s_statement->credit = $request->material_cost;
            $s_statement->supplier_id = $request->supplier_id;
            $s_statement->balance = $current_bal;
            $s_statement->user_id = $user_data->id;
            $s_statement->save();
//dd('248');
            //SAVE TRUCK RENT AND UNLOAD BILL TO EXPENSE IF EXIST
            if ($request->truck_rent != '' && $request->truck_rent > 0) {
//dd('251');
                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->truck_rent > $cash_bal) {
                    // dd('255');
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                $tr_type_id = ExpenseType::where('type_name', 'Truck Rent')->value('id');

                $tr_expense = new Expense();
                $tr_expense->transaction_id = 'PP-' . $pp_no;
                $tr_expense->expense_name = 'Truck Rent';
                $tr_expense->date = $received_date;
                $tr_expense->table_name = 'product_purchases';
                $tr_expense->expense_type_id = $tr_type_id;
                $tr_expense->payment_mode = 'Cash';
                $tr_expense->amount = $request->truck_rent;
                $tr_expense->description = 'Truck Rent for challan no: ' . $request->chalan_no;
                $tr_expense->branchId = $request->branchId;
                $tr_expense->user_id = $user_data->id;
                $tr_expense->expense_type_gen_pur = 1;
                $tr_expense->save();
//dd('273');

                $current_bal = $cash_bal - $request->truck_rent;
                $cash_statement_tr = new CashStatement();
                $cash_statement_tr->transaction_id = 'PP-' . $pp_no;
                $cash_statement_tr->posting_date = date('Y-m-d');
                $cash_statement_tr->table_name = 'product_purchases';
                $cash_statement_tr->debit = $request->truck_rent;
                $cash_statement_tr->balance = $current_bal;
                $cash_statement_tr->description = 'Truck Rent for challan no: ' . $request->chalan_no;
                $cash_statement_tr->receipt_no = '0000';
                $cash_statement_tr->ref_date = $received_date;
                $cash_statement_tr->branchId = $request->branchId;
                $cash_statement_tr->user_id = $user_data->id;
                $cash_statement_tr->save();
            }

            //IF HAS UNLOAD BILL
            if ($request->unload_bill != '' && $request->unload_bill > 0) {
//dd('292');
                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->unload_bill > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                $ub_type_id = ExpenseType::where('type_name', 'Unload Bill')->value('id');

                $ub_expense = new Expense();
                $ub_expense->transaction_id = 'PP-' . $pp_no;
                $ub_expense->expense_name = 'Unload Bill';
                $ub_expense->date = $received_date;
                $ub_expense->table_name = 'product_purchases';
                $ub_expense->expense_type_id = $ub_type_id;
                $ub_expense->payment_mode = 'Cash';
                $ub_expense->amount = $request->unload_bill;
                $ub_expense->description = 'Truck Rent for challan no: ' . $request->chalan_no;
                $ub_expense->user_id = $user_data->id;
                $ub_expense->branchId = $request->branchId;
                $ub_expense->expense_type_gen_pur = 1;
                $ub_expense->save();


                $current_bal = $cash_bal - $request->unload_bill;
                $cash_statement_ub = new CashStatement();
                $cash_statement_ub->transaction_id = 'PP-' . $pp_no;
                $cash_statement_ub->posting_date = date('Y-m-d');
                $cash_statement_ub->table_name = 'product_purchases';
                $cash_statement_ub->debit = $request->unload_bill;
                $cash_statement_ub->balance = $current_bal;
                $cash_statement_ub->description = 'Unload bill for challan no: ' . $request->chalan_no;
                $cash_statement_ub->ref_date = $received_date;
                $cash_statement_ub->branchId = $request->branchId;
                $cash_statement_ub->user_id = $user_data->id;
                $cash_statement_ub->save();
            }

            if ($status) {
                DB::commit();
                Session::flash('message', "Product Purchased Successfully!");
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollBack();
                Session::flash('message', "Product Purchase Failed!");
                Session::flash('m-class', 'alert-danger');
                return back()->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return back()->withInput();
        }

    }

    public function updateProductPurchase(Request $request)
    {
        //  dd('d');
        $rules = [
            'purchase_id' => 'required',
            'dmr_no' => 'required',
            'chalan_no' => 'required',
            'supplier_id' => 'required',
            'product_name_id' => 'required',
            'received_date' => 'required',
            'unit_type' => 'required',
            'product_qty' => 'required',
            'rate_per_unit' => 'required',
            'material_cost' => 'required',
            'total_material_cost' => 'required',
            'file' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm'
        ];

        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    //$file_name = 'product-'.time().'.'.$ext;
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;

                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/');
                    } else {
                        $destinationPath = '/img/files/';
                    }
                    $year_folder = $destinationPath . date("Y") . "/";
                    $month_folder = $year_folder . date("m") . "/";
                    $day_folder = $month_folder . date("d") . "/";

                    !file_exists($year_folder) && mkdir($year_folder, 0777);
                    !file_exists($month_folder) && mkdir($month_folder, 0777);
                    !file_exists($day_folder) && mkdir($day_folder, 0777);

                    $file->move($day_folder, $file_name);
                    $file_names[] = $file_name;
                }

            }
            $com_file_names = implode(',', $file_names);

            $product_purchase = ProductPurchase::find($request->purchase_id);
            $received_date = date('Y-m-d', strtotime($request->received_date));

            //REDUCE STOCK QTY
            $prev_stock = ProductStock::where('product_name_id', $product_purchase->product_name_id)->first();
            $_current_qty = $prev_stock->quantity - $product_purchase->product_qty;

            //IF PRODUCT NOT CHANGED
            if ($product_purchase->product_name_id == $request->product_name_id) {

                if ($product_purchase->product_name->category == 'Stone' && $request->unit_type == 'Ton') {
                    $_current_qty += $request->product_qty * 40;
                } else if ($request->unit_type == 'Ton') {
                    $_current_qty += $request->product_qty * 1000;
                } else {
                    $_current_qty += $request->product_qty;
                }
            } else {
                //CHECK IF CHANGED ITEM EXIST IN PRODUCT STOCK
                $product = ProductName::find($request->product_name_id);
                $stock_exist = ProductStock::where('product_name_id', $request->product_name_id)->first();
                if (!$stock_exist) {
                    $new_stock = new ProductStock();
                    $new_stock->consumption_qty = 0;
                    $new_stock->product_name_id = $request->product_name_id;
                    if ($product->category == 'Stone' && $request->unit_type == 'Ton') {
                        $new_stock->quantity = $request->product_qty * 40;
                        $new_stock->unit_type = 'CFT';
                    } else if ($request->unit_type == 'Ton') {
                        $new_stock->quantity = $request->product_qty * 1000;
                        $new_stock->unit_type = 'KG';
                    } else {
                        $new_stock->quantity = $request->product_qty;
                        $new_stock->unit_type = $request->unit_type;
                    }
                    $new_stock->save();
                } else {
                    $quantity = $stock_exist->quantity;
                    if ($product->category == 'Stone' && $request->unit_type == 'Ton') {
                        $stock_exist->quantity = $request->product_qty * 40 + $quantity;
                    } else if ($request->unit_type == 'Ton') {
                        $stock_exist->quantity = $request->product_qty * 1000 + $quantity;
                    } else {
                        $stock_exist->quantity = $request->product_qty + $quantity;
                    }

                    $stock_exist->save();
                }
            }
            $prev_stock->quantity = $_current_qty;
            $prev_stock->save();


            //UPDATE EXPENSE TABLE
            Expense::where('transaction_id', $product_purchase->transaction_id)
                ->where('expense_name', 'Truck Rent')
                ->update(['amount' => $request->truck_rent]);

            Expense::where('transaction_id', $product_purchase->transaction_id)
                ->where('expense_name', 'Unload Bill')
                ->update(['amount' => $request->unload_bill]);

            //UPDATE CASH STATEMENT
            CashStatement::where('transaction_id', $product_purchase->transaction_id)
                ->where('description', 'LIKE', 'Truck Rent%')
                ->update(['debit' => $request->truck_rent]);

            CashStatement::where('transaction_id', $product_purchase->transaction_id)
                ->where('description', 'LIKE', 'Unload bill%')
                ->update(['debit' => $request->unload_bill]);

            //UPDATE SUPPLIER STATEMENT
            if ($product_purchase->supplier_id == $request->supplier_id) {
                SupplierStatement::where('transaction_id', $product_purchase->transaction_id)->update(['credit' => $request->material_cost]);
            } else {
                $sup_name = Supplier::where('id', $request->supplier_id)->value('name');
                SupplierStatement::where('transaction_id', $product_purchase->transaction_id)
                    ->update([
                            'credit' => $request->material_cost,
                            'supplier_id' => $request->supplier_id,
                            'description' => 'Product Purchase from ' . $sup_name]
                    );
            }

            //update product purchase table
            $product_purchase->dmr_no = $request->dmr_no;
            $product_purchase->chalan_no = $request->chalan_no;
            $product_purchase->supplier_id = $request->supplier_id;
            $product_purchase->vehicle_no = $request->vehicle_no;
            $product_purchase->product_name_id = $request->product_name_id;
            $product_purchase->received_date = $received_date;
            $product_purchase->unit_type = $request->unit_type;
            $product_purchase->product_qty = $request->product_qty;
            $product_purchase->rate_per_unit = $request->rate_per_unit;
            $product_purchase->material_cost = $request->material_cost;
            $product_purchase->truck_rent = $request->truck_rent;
            $product_purchase->unload_bill = $request->unload_bill;
            $product_purchase->total_material_cost = $request->total_material_cost;
            if ($com_file_names != "") {
                $product_purchase->file = $com_file_names;
            }
            $product_purchase->description = $request->description;
            $product_purchase->save();

            DB::commit();
            Session::flash('message', "Product Added Successfully!");
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return back()->withInput();
        }
    }

    public function deleteProductPurchase($trx_id)
    {
        DB::beginTransaction();
        try {
            $product_purchase = ProductPurchase::where('transaction_id', $trx_id)->first();

            $supplier_statement = SupplierStatement::where('transaction_id', $trx_id)->first();
            $supplier_statement->delete();

            //DELETE EXPENSES RELATED TO THIS PURCHASE
            $expenses = Expense::where('transaction_id', $trx_id)->get();
            foreach ($expenses as $expense) {
                $expense->delete();
            }

            //DELETE CASH STATEMENT RELATED TO THIS PURCHASE
            $cash_statements = CashStatement::where('transaction_id', $trx_id)->get();
            foreach ($cash_statements as $cash_statement) {
                $cash_statement->delete();
            }

            //UPDATE PRODUCT STOCK RELATED TO THIS PURCHASE
            $product_id = $product_purchase->product_name_id;
            $product_stock = ProductStock::where('product_name_id', $product_id)->first();

            if ($product_stock) {
                if ($product_stock->product_name->category == 'Stone' && $product_stock->unit_type == 'Ton') {
                    $data['quantity'] = $product_stock->quantity - $product_purchase->product_qty * 40;
                } else if ($product_stock->unit_type == 'Ton') {
                    $data['quantity'] = $product_stock->quantity - $product_purchase->product_qty * 1000;
                } else {
                    $data['quantity'] = $product_stock->quantity - $product_purchase->product_qty;
                }

                $product_stock->update($data);
            }

            $status = $product_purchase->delete();
            if ($status) {
                DB::commit();
                Session::flash('message', "Product Purchase Deleted Successfully!");
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollBack();
                Session::flash('message', "Product Purchase Deleted failed!");
                Session::flash('m-class', 'alert-danger');
                return back()->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return back()->withInput();
        }

    }

    public function purchaseCheck(Request $request)
    {
        $rules = [
            'checkbox' => 'required',
            'bill_no' => 'required|unique:product_purchases',
        ];

        $this->validate($request, $rules);

        $ids = $request->checkbox;
        $bill_no = $request->bill_no;
        $adjustmet_qty = $request->adjustment_qty;
        $adjustment_cost = $request->adjustment_cost;

        if ($ids == "" && $bill_no == "") {
            Session::flash('message', 'No checkbox is selected or Bill Number is not entered!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
        $pids = array_filter($ids);

        $product_row_id = $pids[0];
        $purchase_pro_row = ProductPurchase::where('id', $product_row_id)->first();

        //checking product rows for same supplier and product
        foreach ($pids as $pid) {
            $row = ProductPurchase::where('id', $pid)->first();
            if ($row->product_name_id != $purchase_pro_row->product_name_id) {
                Session::flash('message', 'Select Same products for bill checking!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back();
            }

            if ($row->supplier_id != $purchase_pro_row->supplier_id) {
                Session::flash('message', 'Select Same suppliers for bill checking!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back();
            }
        }

        DB::beginTransaction();
        try {
            //return $purchase_pro_row->product_name_id;
            $count = count($pids);
            $db_count = 0;

            foreach ($pids as $pid) {
                ProductPurchase::where('id', $pid)->update(['check_status' => 1, 'bill_no' => $bill_no]);
                $db_count++;
            }

            //update stock table
            if ($adjustmet_qty != "") {
                $stock_row = ProductStock::where('product_name_id', $purchase_pro_row->product_name_id)->first();

                $pre_qty = $stock_row->quantity;
                $present_qty = $pre_qty - $adjustmet_qty;
                $stock_row->quantity = $present_qty;
                $stock_row->save();
            }

            //insert into supplier statement
            if ($adjustment_cost != "") {
                $sup_row = new SupplierStatement();
                $sup_row->transaction_id = "BILLAD-" . $bill_no;
                $sup_row->posting_date = date('Y-m-d');
                $sup_row->description = "Bill no: " . $bill_no . ", Adjustment Quantity: " . $adjustmet_qty;
                $sup_row->table_name = "";
                if ($purchase_pro_row->unit_type == "Ton") {
                    $sup_row->adjustment_qty = $adjustmet_qty * 1000;
                } else {
                    $sup_row->adjustment_qty = $adjustmet_qty;
                }

                $sup_row->debit = $adjustment_cost;
                $sup_row->product_name_id = $purchase_pro_row->product_name_id;
                $sup_row->supplier_id = $purchase_pro_row->supplier_id;
                $sup_row->save();
            }

            if ($db_count == $count) {
                DB::commit();
                Session::flash('message', 'List has been checked successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                throw new \Exception('Something is wrong!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function viewProductCheckDetails(Request $request)
    {
        $purchases = ProductPurchase::where('bill_no', $request->bill_no)->get();
        $product_names = ProductName::all();
        $suppliers = Supplier::orderBy('name', 'ASC')->get();
        return view('admin.product.view_check_product', compact('purchases', 'product_names', 'suppliers'));
    }

}
