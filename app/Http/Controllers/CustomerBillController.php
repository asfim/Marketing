<?php

namespace App\Http\Controllers;

use App\Models\CashStatement;
use App\Models\Config;
use App\Models\Customer;
use App\Models\DemoBill;
use App\Models\DemoProductConsumption;
use App\Models\DemoProductSale;
use App\Models\Expense;
use App\Models\ExpenseType;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ProductName;
use App\Models\ProductStock;
use App\Models\CustomerProject;
use App\Models\MixDesign;
use App\Models\ProductSale;
use App\Models\CustomerStatement;
use App\Models\Bill;
use App\Models\EngineerTipsStatement;
use App\Models\ProductConsumption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerBillController extends Controller
{
    public function loadCustomerProjectPSI(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $response = array(
            'projects' => $customer->projects,
            'mix_design_psi' => $customer->mixDesigns,
        );
        return response()->json($response);
    }


    public function generateDemoBillView(Request $request)
    {

        $challan_ids = $request->input('checkbox');
        if ($challan_ids == "") {
            Session::flash('message', "No checkbox is selected!");
            Session::flash('m-class', 'alert-danger');
            return back()->withInput();
        }
        $txt_ids = implode(',', $challan_ids);

        $sl = 1;
        $table_rows = "";
        $customer = Customer::find($request->customer_id);
        $project_id = $request->project_id;
        $mix_design_id = $request->mix_design_id;
        $mix_design = MixDesign::find($request->mix_design_id);
        $psi = $mix_design->psi;
        $rate = $mix_design->rate;
        $total_cuM = 0;
        $total_amount = 0;   // ✅ initialize here

        foreach ($challan_ids as $id) {
            $chalan_row = ProductSale::where('id', $id)->first();
            $total_cuM += $chalan_row->cuM;
            $qty_cft = $chalan_row->cuM * 35.315;

            // ✅ Apply rate condition
            if ($chalan_row->rate <= 0.00) {
                $row_rate = $chalan_row->mix_design->rate;
                $row_total = $qty_cft * $row_rate;
            } else {
                $row_rate = $chalan_row->rate;
                $row_total = $qty_cft * $row_rate;
            }

            $total_amount += $row_total;  // ✅ now safe to use

            $table_rows .= '<tr>'
                . '<td>' . $sl . '</td>'
                . '<td>' . $chalan_row->challan_no . '</td>'
                . '<td>' . $chalan_row->mix_design->psi . '</td>'
                . '<td>' . number_format($chalan_row->cuM, 2) . '</td>'
                . '<td>' . number_format($qty_cft, 2) . '</td>'
                . '<td>' . $chalan_row->sell_date . '</td>'
                . '<td>' . number_format($row_rate, 2) . '</td>'
                . '<td>' . number_format($row_total, 2) . '</td>'
                . '<td>' . $chalan_row->description . '</td>'
                . '</tr>';
            $sl++;
        }

        $total_cft = $total_cuM * 35.315;

        return view('admin.customer.add_demo_bill', compact('table_rows', 'project_id', 'customer', 'mix_design_id', 'total_cuM', 'total_cft', 'total_amount', 'txt_ids', 'psi', 'rate'));
    }


    public function generateBillView(Request $request)
    {

        $challan_ids = $request->input('checkbox');
        if ($challan_ids == "") {
            Session::flash('message', "No checkbox is selected!");
            Session::flash('m-class', 'alert-danger');
            return back()->withInput();
        }
        $txt_ids = implode(',', $challan_ids);

        $sl = 1;
        $table_rows = "";
        $customer = Customer::find($request->customer_id);
        $project_id = $request->project_id;
        $mix_design_id = $request->mix_design_id;
        $mix_design = MixDesign::find($request->mix_design_id);
        $psi = $mix_design->psi;
        $rate = $mix_design->rate;
        $total_cuM = 0;
        $total_amount = 0;   // ✅ initialize here

        foreach ($challan_ids as $id) {
            $chalan_row = ProductSale::where('id', $id)->first();
            $total_cuM += $chalan_row->cuM;
            $qty_cft = $chalan_row->cuM * 35.315;

            // ✅ Apply rate condition
            if ($chalan_row->rate <= 0.00) {
                $row_rate = $chalan_row->mix_design->rate;
                $row_total = $qty_cft * $row_rate;
            } else {
                $row_rate = $chalan_row->rate;
                $row_total = $qty_cft * $row_rate;
            }

            $total_amount += $row_total;  // ✅ now safe to use

            $table_rows .= '<tr>'
                . '<td>' . $sl . '</td>'
                . '<td>' . $chalan_row->challan_no . '</td>'
                . '<td>' . $chalan_row->mix_design->psi . '</td>'
                . '<td>' . number_format($chalan_row->cuM, 2) . '</td>'
                . '<td>' . number_format($qty_cft, 2) . '</td>'
                . '<td>' . $chalan_row->sell_date . '</td>'
                . '<td>' . number_format($row_rate, 2) . '</td>'
                . '<td>' . number_format($row_total, 2) . '</td>'
                . '<td>' . $chalan_row->description . '</td>'
                . '</tr>';
            $sl++;
        }

        $total_cft = $total_cuM * 35.315;

        return view('admin.customer.add_bill', compact('table_rows', 'project_id', 'customer', 'mix_design_id', 'total_cuM', 'total_cft', 'total_amount', 'txt_ids', 'psi', 'rate'));
    }


    public function saveDemoBill(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'bill_date' => 'required',
            'concrete_method' => 'required',
            'total_cft' => 'required',
            'total_cuM' => 'required',
            'total_amount' => 'required',
            'ids' => 'required',
            'mix_design_id' => 'required',
            'rate' => 'required'
        ];
        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            $user_data = Auth::user();
            $bill_date = date('Y-m-d', strtotime($request->bill_date));
            //fetch mix design data
            $mix_design = MixDesign::find($request->mix_design_id);

            //insert into bill table
            //generate invoice no
            $bill_id = DemoBill::max('id');
            if ($bill_id == "") {
                $invoice_no = 1;
                $tran_id = 'CBILL-1';
            } else {
                $invoice_no = $bill_id + 1;
                $tran_id = 'CBILL-' . $invoice_no;
            }

            $pump_charge = ($request->pump_charge == "") ? 0 : $request->pump_charge;

            //STONE CONSUMPTION
            $a = 0;
            $stone_id_array = array_filter(explode(',', $mix_design->stone_id));
            $stone_qty_array = array_filter(explode(',', $mix_design->stone_quantity));
            foreach ($stone_id_array as $stone_id) {
                //convert stone kg to cft
                $conversion_rate = ProductName::where('id', $stone_id)->value('conversion_rate');
                $stone_cft = $stone_qty_array[$a] * $request->total_cuM / $conversion_rate;
                //insert into product_consumption table
                $cns_statement = new DemoProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $stone_id;
                $cns_statement->unit_type = 'CFT';
                $cns_statement->consumption_qty = $stone_cft;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();

                //update product_stock
                $stone_stock_row = ProductStock::where('product_name_id', $stone_id)->first();
                if ($stone_stock_row && ($stone_stock_row->quantity - $stone_cft) >= 0) {
                    $stock_id = $stone_stock_row->id;
                    $updated_qty = $stone_stock_row->quantity - $stone_cft;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $stone_cft;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    // $row->save();
                } else {
                    throw new \Exception('This Stone Stock is not available please purchase now');
                }
                $a++;
            }

            //SAND CONSUMPTION
            $sand_id_array = array_filter(explode(',', $mix_design->sand_id));
            $sand_qty_array = array_filter(explode(',', $mix_design->sand_quantity));
            $b = 0;
            foreach ($sand_id_array as $sand_id) {
                //CONVERT SAND KG TO CFT
                $conversion_rate = ProductName::where('id', $sand_id)->value('conversion_rate');
                $sand_cft = $sand_qty_array[$b] * $request->total_cuM / $conversion_rate;
                //insert into product_consumption table
                $cns_statement = new DemoProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $sand_id;
                $cns_statement->unit_type = 'CFT';
                $cns_statement->consumption_qty = $sand_cft;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();
                //update product_stock
                $sand_stock_row = ProductStock::where('product_name_id', $sand_id)->first();
                if ($sand_stock_row && ($sand_stock_row->quantity - $sand_cft) >= 0) {
                    $stock_id = $sand_stock_row->id;
                    $updated_qty = $sand_stock_row->quantity - $sand_cft;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $sand_cft;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    // $row->save();
                } else {
                    throw new \Exception('This Sand Stock is not available please purchase now');
                }
                $b++;
            }

            //CEMENT CONSUMPTION
            $cement_id_array = array_filter(explode(',', $mix_design->cement_id));
            $cement_qty_array = array_filter(explode(',', $mix_design->cement_quantity));
            $d = 0;
            foreach ($cement_id_array as $cement_id) {
                $consumption_qty = $cement_qty_array[$d] * $request->total_cuM;
                //insert into product_consumption table
                $cns_statement = new DemoProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $cement_id;
                $cns_statement->unit_type = 'KG';
                $cns_statement->consumption_qty = $consumption_qty;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();
                //update product_stock
                $cement_stock_row = ProductStock::where('product_name_id', $cement_id)->first();
                if ($cement_stock_row && ($cement_stock_row->quantity - $consumption_qty) >= 0) {
                    $stock_id = $cement_stock_row->id;
                    $updated_qty = $cement_stock_row->quantity - $consumption_qty;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $consumption_qty;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    //   $row->save();
                } else {
                    throw new \Exception('This Cement Stock is not available please purchase now');
                }
                $d++;
            }

            //CHEMICAL CONSUMPTION
            $chemical_id_array = array_filter(explode(',', $mix_design->chemical_id));
            $chemical_qty_array = array_filter(explode(',', $mix_design->chemical_quantity));
            $c = 0;
            foreach ($chemical_id_array as $chemical_name) {
                //insert into product_consumption table
                $consumption_qty = $chemical_qty_array[$c] * $request->total_cuM;
                $cns_statement = new DemoProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $chemical_name;
                $cns_statement->unit_type = 'KG';
                $cns_statement->consumption_qty = $chemical_qty_array[$c] * $request->total_cuM;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();
                //update product_stock
                $chemical_stock_row = ProductStock::where('product_name_id', $chemical_name)->first();
                if ($chemical_stock_row && ($chemical_stock_row->quantity - $consumption_qty) >= 0) {
                    $stock_id = $chemical_stock_row->id;
                    $updated_qty = $chemical_stock_row->quantity - $consumption_qty;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $consumption_qty;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    //    $row->save();
                } else {
                    throw new \Exception('This Chemical Stock is not available please purchase now');
                }
                $c++;
            }


            //after consumption save in bill table
            $description = "Bill no: " . $invoice_no . ", psi:" . $mix_design->psi . ", Pump Charge: " . $pump_charge;
            $bill = new DemoBill();
            $bill->transaction_id = $tran_id;
            $bill->invoice_no = $invoice_no;
            $bill->total_cuM = $request->total_cuM;
            $bill->total_cft = $request->total_cft;
            $bill->total_amount = $request->total_amount + $pump_charge;
            $bill->total_amount_before_discount = $request->total_amount + $pump_charge;
            $bill->bill_date = $bill_date;
            $bill->psi = $mix_design->psi;
            $bill->rate = $mix_design->rate;
            $bill->concrete_method = $request->concrete_method;
            $bill->pump_charge = $pump_charge;
            $bill->eng_tips = $request->eng_tips ?? 0;
            $bill->ait = $request->ait ?? 0;
            $bill->vat = $request->vat ?? 0;
            $bill->customer_id = $request->customer_id;
            $bill->user_id = $user_data->id;
            $bill->branchId = $user_data->branchId;
            $bill->description = $request->description;
            $bill->save();

            //update product_sales table
            $challan_ids = explode(',', $request->input('ids'));
            foreach ($challan_ids as $challan_id) {
                $row = DemoProductSale::find($challan_id);
                $row->invoice_no = $invoice_no;
                $row->status = 2;
                $row->save();
               // Log::info($row);
            }

            //insert into customer_statements
            $customer = Customer::find($request->customer_id);
            $c_statement = new CustomerStatement();
            $c_statement->transaction_id = $tran_id;
            $c_statement->posting_date = date('Y-m-d');
            $c_statement->description = $description;
            $c_statement->table_name = 'bills';
            $c_statement->credit = $request->total_amount + $pump_charge;
            $c_statement->customer_id = $request->customer_id;
            $c_statement->balance = $customer->balance() + $request->total_amount;;
            $c_statement->branchId = $user_data->branchId;
            $c_statement->user_id = $user_data->id;
            //  $c_statement->save();


            //INSERT INTO ENGINEER TIPS
            if ($request->eng_tips > 0) {

                //CHECK CASH BALANCE
                $cash_bal = cashBalance($user_data->branchId);
                if ($request->eng_tips > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                $e_statement = new EngineerTipsStatement();
                $e_statement->transaction_id = $tran_id;
                $e_statement->posting_date = date('Y-m-d');
                $e_statement->customer_name = $customer->name;
                $e_statement->description = $description;
                $e_statement->table_name = 'bills';
                $e_statement->debit = $request->eng_tips;
                $e_statement->customer_id = $request->customer_id;
                $e_statement->user_id = $user_data->id;
                $e_statement->branchId = $user_data->branchId;
                // $e_statement->save();

                $ex_type_id = ExpenseType::where('type_name', 'Engineer Tips')->value('id');

                $tr_expense = new Expense();
                $tr_expense->transaction_id = $tran_id;
                $tr_expense->expense_name = 'Engineer Tips';
                $tr_expense->date = $bill_date;
                $tr_expense->table_name = 'bills';
                $tr_expense->expense_type_id = $ex_type_id;
                $tr_expense->payment_mode = 'Cash';
                $tr_expense->amount = $request->eng_tips;
                $tr_expense->description = 'Engineer Tips for bill no: ' . $tran_id;
                $tr_expense->user_id = $user_data->id;
                $tr_expense->branchId = $user_data->branchId;
                //$tr_expense->save();

                $current_bal = $cash_bal - $request->eng_tips;
                $cash_statement_tr = new CashStatement();
                $cash_statement_tr->transaction_id = $tran_id;
                $cash_statement_tr->posting_date = date('Y-m-d');
                $cash_statement_tr->table_name = 'bills';
                $cash_statement_tr->debit = $request->eng_tips;
                $cash_statement_tr->balance = $current_bal;
                $cash_statement_tr->description = 'Engineer Tips for bill no: ' . $tran_id;
                $cash_statement_tr->receipt_no = '';
                $cash_statement_tr->ref_date = $bill_date;
                $cash_statement_tr->branchId = $user_data->branchId;
                $cash_statement_tr->user_id = $user_data->id;
                //  $cash_statement_tr->save();
            }


            DB::commit();
            Session::flash('message', 'Bill Created Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('customer.profile', $request->customer_id);
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->route('customer.profile', $request->customer_id);
        }
    }

    public function saveBill(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'bill_date' => 'required',
            'concrete_method' => 'required',
            'total_cft' => 'required',
            'total_cuM' => 'required',
            'total_amount' => 'required',
            'ids' => 'required',
            'mix_design_id' => 'required',
            'rate' => 'required'
        ];
        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            $user_data = Auth::user();
            $bill_date = date('Y-m-d', strtotime($request->bill_date));
            //fetch mix design data
            $mix_design = MixDesign::find($request->mix_design_id);

            //insert into bill table
            //generate invoice no
            $bill_id = Bill::max('id');
            if ($bill_id == "") {
                $invoice_no = 1;
                $tran_id = 'CBILL-1';
            } else {
                $invoice_no = $bill_id + 1;
                $tran_id = 'CBILL-' . $invoice_no;
            }

            $pump_charge = ($request->pump_charge == "") ? 0 : $request->pump_charge;

            //STONE CONSUMPTION
            $a = 0;
            $stone_id_array = array_filter(explode(',', $mix_design->stone_id));
            $stone_qty_array = array_filter(explode(',', $mix_design->stone_quantity));
            foreach ($stone_id_array as $stone_id) {
                //convert stone kg to cft
                $conversion_rate = ProductName::where('id', $stone_id)->value('conversion_rate');
                $stone_cft = $stone_qty_array[$a] * $request->total_cuM / $conversion_rate;
                //insert into product_consumption table
                $cns_statement = new ProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $stone_id;
                $cns_statement->unit_type = 'CFT';
                $cns_statement->consumption_qty = $stone_cft;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();

                //update product_stock
                $stone_stock_row = ProductStock::where('product_name_id', $stone_id)->first();
                if ($stone_stock_row && ($stone_stock_row->quantity - $stone_cft) >= 0) {
                    $stock_id = $stone_stock_row->id;
                    $updated_qty = $stone_stock_row->quantity - $stone_cft;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $stone_cft;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    $row->save();
                } else {
                    throw new \Exception('This Stone Stock is not available please purchase now');
                }
                $a++;
            }

            //SAND CONSUMPTION
            $sand_id_array = array_filter(explode(',', $mix_design->sand_id));
            $sand_qty_array = array_filter(explode(',', $mix_design->sand_quantity));
            $b = 0;
            foreach ($sand_id_array as $sand_id) {
                //CONVERT SAND KG TO CFT
                $conversion_rate = ProductName::where('id', $sand_id)->value('conversion_rate');
                $sand_cft = $sand_qty_array[$b] * $request->total_cuM / $conversion_rate;
                //insert into product_consumption table
                $cns_statement = new ProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $sand_id;
                $cns_statement->unit_type = 'CFT';
                $cns_statement->consumption_qty = $sand_cft;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();
                //update product_stock
                $sand_stock_row = ProductStock::where('product_name_id', $sand_id)->first();
                if ($sand_stock_row && ($sand_stock_row->quantity - $sand_cft) >= 0) {
                    $stock_id = $sand_stock_row->id;
                    $updated_qty = $sand_stock_row->quantity - $sand_cft;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $sand_cft;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    $row->save();
                } else {
                    throw new \Exception('This Sand Stock is not available please purchase now');
                }
                $b++;
            }

            //CEMENT CONSUMPTION
            $cement_id_array = array_filter(explode(',', $mix_design->cement_id));
            $cement_qty_array = array_filter(explode(',', $mix_design->cement_quantity));
            $d = 0;
            foreach ($cement_id_array as $cement_id) {
                $consumption_qty = $cement_qty_array[$d] * $request->total_cuM;
                //insert into product_consumption table
                $cns_statement = new ProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $cement_id;
                $cns_statement->unit_type = 'KG';
                $cns_statement->consumption_qty = $consumption_qty;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();
                //update product_stock
                $cement_stock_row = ProductStock::where('product_name_id', $cement_id)->first();
                if ($cement_stock_row && ($cement_stock_row->quantity - $consumption_qty) >= 0) {
                    $stock_id = $cement_stock_row->id;
                    $updated_qty = $cement_stock_row->quantity - $consumption_qty;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $consumption_qty;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    $row->save();
                } else {
                    throw new \Exception('This Cement Stock is not available please purchase now');
                }
                $d++;
            }

            //CHEMICAL CONSUMPTION
            $chemical_id_array = array_filter(explode(',', $mix_design->chemical_id));
            $chemical_qty_array = array_filter(explode(',', $mix_design->chemical_quantity));
            $c = 0;
            foreach ($chemical_id_array as $chemical_name) {
                //insert into product_consumption table
                $consumption_qty = $chemical_qty_array[$c] * $request->total_cuM;
                $cns_statement = new ProductConsumption();
                $cns_statement->transaction_id = $tran_id;
                $cns_statement->consumption_date = $bill_date;
                $cns_statement->product_id = $chemical_name;
                $cns_statement->unit_type = 'KG';
                $cns_statement->consumption_qty = $chemical_qty_array[$c] * $request->total_cuM;
                $cns_statement->psi = $mix_design->psi;
                $cns_statement->customer_id = $request->customer_id;
                $cns_statement->userId = $user_data->id;
                $cns_statement->save();
                //update product_stock
                $chemical_stock_row = ProductStock::where('product_name_id', $chemical_name)->first();
                if ($chemical_stock_row && ($chemical_stock_row->quantity - $consumption_qty) >= 0) {
                    $stock_id = $chemical_stock_row->id;
                    $updated_qty = $chemical_stock_row->quantity - $consumption_qty;
                    $upd_consumption_qty = $stone_stock_row->consumption_qty + $consumption_qty;
                    $row = ProductStock::find($stock_id);
                    $row->quantity = $updated_qty;
                    $row->consumption_qty = $upd_consumption_qty;
                    $row->save();
                } else {
                    throw new \Exception('This Chemical Stock is not available please purchase now');
                }
                $c++;
            }


            //after consumption save in bill table
            $description = "Bill no: " . $invoice_no . ", psi:" . $mix_design->psi . ", Pump Charge: " . $pump_charge;
            $bill = new Bill();
            $bill->transaction_id = $tran_id;
            $bill->invoice_no = $invoice_no;
            $bill->total_cuM = $request->total_cuM;
            $bill->total_cft = $request->total_cft;
            $bill->total_amount = $request->total_amount + $pump_charge;
            $bill->total_amount_before_discount = $request->total_amount + $pump_charge;
            $bill->bill_date = $bill_date;
            $bill->psi = $mix_design->psi;
            $bill->rate = $mix_design->rate;
            $bill->concrete_method = $request->concrete_method;
            $bill->pump_charge = $pump_charge;
            $bill->eng_tips = $request->eng_tips ?? 0;
            $bill->ait = $request->ait ?? 0;
            $bill->vat = $request->vat ?? 0;
            $bill->customer_id = $request->customer_id;
            $bill->user_id = $user_data->id;
            $bill->branchId = $user_data->branchId;
            $bill->description = $request->description;
            $bill->save();

            //update product_sales table
            $challan_ids = explode(',', $request->input('ids'));
            foreach ($challan_ids as $challan_id) {
                $row = ProductSale::find($challan_id);
                $row->invoice_no = $invoice_no;
                $row->status = 0;
                $row->save();
            }

            //insert into customer_statements
            $customer = Customer::find($request->customer_id);
            $c_statement = new CustomerStatement();
            $c_statement->transaction_id = $tran_id;
            $c_statement->posting_date = date('Y-m-d');
            $c_statement->description = $description;
            $c_statement->table_name = 'bills';
            $c_statement->credit = $request->total_amount + $pump_charge;
            $c_statement->customer_id = $request->customer_id;
            $c_statement->balance = $customer->balance() + $request->total_amount;;
            $c_statement->branchId = $user_data->branchId;
            $c_statement->user_id = $user_data->id;
            $c_statement->save();


            //INSERT INTO ENGINEER TIPS
            if ($request->eng_tips > 0) {

                //CHECK CASH BALANCE
                $cash_bal = cashBalance($user_data->branchId);
                if ($request->eng_tips > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                $e_statement = new EngineerTipsStatement();
                $e_statement->transaction_id = $tran_id;
                $e_statement->posting_date = date('Y-m-d');
                $e_statement->customer_name = $customer->name;
                $e_statement->description = $description;
                $e_statement->table_name = 'bills';
                $e_statement->debit = $request->eng_tips;
                $e_statement->customer_id = $request->customer_id;
                $e_statement->user_id = $user_data->id;
                $e_statement->branchId = $user_data->branchId;
                $e_statement->save();

                $ex_type_id = ExpenseType::where('type_name', 'Engineer Tips')->value('id');

                $tr_expense = new Expense();
                $tr_expense->transaction_id = $tran_id;
                $tr_expense->expense_name = 'Engineer Tips';
                $tr_expense->date = $bill_date;
                $tr_expense->table_name = 'bills';
                $tr_expense->expense_type_id = $ex_type_id;
                $tr_expense->payment_mode = 'Cash';
                $tr_expense->amount = $request->eng_tips;
                $tr_expense->description = 'Engineer Tips for bill no: ' . $tran_id;
                $tr_expense->user_id = $user_data->id;
                $tr_expense->branchId = $user_data->branchId;
                $tr_expense->save();

                $current_bal = $cash_bal - $request->eng_tips;
                $cash_statement_tr = new CashStatement();
                $cash_statement_tr->transaction_id = $tran_id;
                $cash_statement_tr->posting_date = date('Y-m-d');
                $cash_statement_tr->table_name = 'bills';
                $cash_statement_tr->debit = $request->eng_tips;
                $cash_statement_tr->balance = $current_bal;
                $cash_statement_tr->description = 'Engineer Tips for bill no: ' . $tran_id;
                $cash_statement_tr->receipt_no = '';
                $cash_statement_tr->ref_date = $bill_date;
                $cash_statement_tr->branchId = $user_data->branchId;
                $cash_statement_tr->user_id = $user_data->id;
                $cash_statement_tr->save();
            }


            DB::commit();
            Session::flash('message', 'Bill Created Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('customer.profile', $request->customer_id);
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->route('customer.profile', $request->customer_id);
        }
    }

    public function viewBills(Request $request)
    {

        if ($request->has('date_range') && $request->filled('date_range')) {
            $date_range = date_range_to_arr($request->date_range);
        }elseif ($request->date_range == '' && $request->search_text == '') {
            $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        }
        $bills = new Bill();

        if (isset($date_range)) {
            $bills = $bills->whereBetween('bill_date',
                [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d'),
            ]);
        }

        if ($request->search_text) {
            $bills = $bills->where(function ($query) use ($request) {
                $query->where('invoice_no', 'LIKE', '%' . $request->search_text . '%')
                    ->orwhere('psi', '=', $request->search_text)
                    ->orwhere('concrete_method', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhereHas('customer', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
                    });
            });
        }

        $bills = $bills->orderBy('id', 'DESC')->get();

        return view('admin.customer.view_bill', compact('bills'));
    }

    public function viewDemoBillDetails($con_no)
    {

       // $challan_rows = DemoProductSale::where('invoice_no', $con_no)->orderBy('challan_no', 'ASC')->get();
        $challan_rows = DemoProductSale::where('invoice_no', $con_no)
            ->where('status', 2)
            ->orderBy('challan_no', 'ASC')
            ->get();

        $bill_row = DemoBill::where('invoice_no', $con_no)->first();
        $challan_row = DemoProductSale::where('invoice_no', $con_no)->where('status', 2)->orderBy('challan_no', 'ASC')->first();
        $project_row = "";

        $rate_per_cft = 0;
        if ($bill_row != "") {
            $rate_per_cft = $bill_row->rate;
        }

        // Format bill date as ddmmyyyy (e.g. 03062025)
        $new_bill_date = date('d-m-Y', strtotime($bill_row->bill_date));
        $bill_date_compact = str_replace("-", "", $new_bill_date);

        // Get the customer's name for initials
        $customer = Customer::where('id', $bill_row->customer_id)->first();
        $client_name = $customer ? $customer->name : '';

        // Build initials from client name (first letter of each word, uppercase)
        $initials = '';
        foreach (explode(' ', $client_name) as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        // Final invoice number format: Initials + Date + Bill Row ID
        $invoice_no = $initials . $bill_date_compact . $bill_row->invoice_no;
        $user_bill_no = $bill_row ? $bill_row->user_bill_no : '';
        $user_work_order_no = $bill_row ? $bill_row->user_work_order_no : '';
        if ($challan_row != "") {
            $project_row = CustomerProject::where('id', $challan_row->project_id)->first();
        }
        $grand_total = 0;
        if ($bill_row) {
            $grand_total = $bill_row->total_amount
                + ($bill_row->total_amount * $bill_row->ait / 100)
                + ($bill_row->total_amount * $bill_row->vat / 100);
        }

        $grand_total_in_words = $this->numberToWords((int)round($grand_total)) . ' Taka Only';

        $demobill = 1;

        return view('admin.customer.view_bill_details', compact('demobill', 'user_work_order_no', 'user_bill_no', 'challan_rows', 'bill_row', 'project_row', 'invoice_no', 'rate_per_cft',
            'grand_total',
            'grand_total_in_words'));
    }

    public function viewBillDetails($con_no)
    {
        $challan_rows = ProductSale::where('invoice_no', $con_no)->orderBy('challan_no', 'ASC')->get();
        $bill_row = Bill::where('invoice_no', $con_no)->first();
        $challan_row = ProductSale::where('invoice_no', $con_no)->orderBy('challan_no', 'ASC')->first();
        $project_row = "";

        $rate_per_cft = 0;
        if ($bill_row != "") {
            $rate_per_cft = $bill_row->rate;
        }

        // Format bill date as ddmmyyyy (e.g. 03062025)
        $new_bill_date = date('d-m-Y', strtotime($bill_row->bill_date));
        $bill_date_compact = str_replace("-", "", $new_bill_date);

        // Get the customer's name for initials
        $customer = Customer::where('id', $bill_row->customer_id)->first();
        $client_name = $customer ? $customer->name : '';

        // Build initials from client name (first letter of each word, uppercase)
        $initials = '';
        foreach (explode(' ', $client_name) as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        // Final invoice number format: Initials + Date + Bill Row ID
        $invoice_no = $initials . $bill_date_compact . $bill_row->invoice_no;
        $user_bill_no = $bill_row ? $bill_row->user_bill_no : '';
        $user_work_order_no = $bill_row ? $bill_row->user_work_order_no : '';
        if ($challan_row != "") {
            $project_row = CustomerProject::where('id', $challan_row->project_id)->first();
        }
        $grand_total = 0;
        if ($bill_row) {
            $grand_total = $bill_row->total_amount
                + ($bill_row->total_amount * $bill_row->ait / 100)
                + ($bill_row->total_amount * $bill_row->vat / 100);
        }

        $grand_total_in_words = $this->numberToWords((int)round($grand_total)) . ' Taka Only';
$demobill =0;
        return view('admin.customer.view_bill_details', compact('demobill','user_work_order_no', 'user_bill_no', 'challan_rows', 'bill_row', 'project_row', 'invoice_no', 'rate_per_cft',
            'grand_total',
            'grand_total_in_words'));
    }

    public function numberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lac',
            10000000 => 'crore'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            throw new Exception('Number is too large');
        }

        if ($number < 0) {
            return $negative . $this->numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos((string)$number, '.') !== false) {
            list($number, $fraction) = explode('.', (string)$number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;

            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;

            case $number < 1000:
                $hundreds = (int)($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->numberToWords($remainder);
                }
                break;

            default:
                $baseUnits = [10000000, 100000, 1000, 100];
                foreach ($baseUnits as $baseUnit) {
                    if ($number >= $baseUnit) {
                        $numBaseUnits = (int)($number / $baseUnit);
                        $remainder = $number % $baseUnit;
                        $string = $this->numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                        if ($remainder) {
                            $string .= $remainder < 100 ? $conjunction : $separator;
                            $string .= $this->numberToWords($remainder);
                        }
                        break;
                    }
                }
                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string)$fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }

        return ucfirst($string);
    }

    public function updateUserBillNo(Request $request, $id)
    {

        if ($request->demobill == 1) {
            $bill = DemoBill::findOrFail($id);
            $bill->user_bill_no = $request->input('user_bill_no');
            $bill->save();
        } else {
            $bill = Bill::findOrFail($id);
            $bill->user_bill_no = $request->input('user_bill_no');
            $bill->save();
        }
        return redirect()->back()->with('success', 'User Bill No updated successfully.');
    }

    public function updateUserWorkOrderNo(Request $request, $id)
    {


        if ($request->demobill == 1) {

            $bill = DemoBill::findOrFail($id);
            $bill->user_work_order_no = $request->input('user_work_order_no');
            $bill->save();

        } else {
            $bill = Bill::findOrFail($id);
            $bill->user_work_order_no = $request->input('user_work_order_no');
            $bill->save();
        }
        return redirect()->back()->with('success', 'Work Order Number updated successfully.');
    }





//    public function viewBillDetails($con_no)
//    {
//        $challan_rows = ProductSale::where('invoice_no',$con_no)->orderBy('invoice_no','ASC')->get();
//        $bill_row = Bill::where('invoice_no',$con_no)->first();
//        $challan_row = ProductSale::where('invoice_no',$con_no)->orderBy('invoice_no','ASC')->first();
//        $project_row = "";
//
//        $rate_per_cft = 0;
//        if($bill_row != "") {
//            $rate_per_cft = $bill_row->rate;
//        }
//
//        $new_bill_date = date('d-m-Y',strtotime($bill_row->bill_date));
//        $invoice_prefix = Config::where('config_title', 'invoice_prefix')->first()->value;
//        $invoice_no = $invoice_prefix.'/'.str_replace("-", "", $new_bill_date).'/'.$bill_row->invoice_no;
//
//        if($challan_row != "") {
//            $project_row = CustomerProject::where('id',$challan_row->project_id)->first();
//        }
//
//        return view('admin.customer.view_bill_details',  compact('challan_rows','bill_row','project_row','invoice_no','rate_per_cft'));
//    }

    public function demoDeleteBill($id)
    {
        $bill = DemoBill::find($id);

        DB::beginTransaction();
        try {
                $consumptions = DemoProductConsumption::where('transaction_id',$bill->transaction_id)->delete();
//            foreach ($consumptions as $consumption) {
//                $stock_row = ProductStock::where('product_name_id', $consumption->product_id)->first();
//                $updated_qty = $stock_row->quantity + $consumption->consumption_qty;
//                $upd_consumption_qty = $stock_row->consumption_qty - $consumption->consumption_qty;
//                $stock_row->quantity = $updated_qty;
//                $stock_row->consumption_qty = $upd_consumption_qty;
//                $stock_row->save();
//            }

            //update product_sales table
            DemoProductSale::where('invoice_no', $bill->invoice_no)->update(['invoice_no' => null, 'status' => 1]);

            //insert into customer_statements
            //  CustomerStatement::where('transaction_id',$bill->transaction_id)->delete();

            //INSERT INTO ENGINEER TIPS
//            if ($bill->eng_tips > 0) {
//                EngineerTipsStatement::where('transaction_id',$bill->transaction_id)->delete();
//                Expense::where('transaction_id',$bill->transaction_id)->delete();
//                CashStatement::where('transaction_id',$bill->transaction_id)->delete();
//            }
//  Delete updated bill record
//            DB::table('updated_bill')->where('bill_id', $bill->id)->delete();
            $bill->delete();

            DB::commit();
            Session::flash('message', 'Bill Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function deleteBill($id)
    {
        $bill = Bill::find($id);

        DB::beginTransaction();
        try {
            $consumptions = ProductConsumption::where('transaction_id', $bill->transaction_id);
            foreach ($consumptions as $consumption) {
                $stock_row = ProductStock::where('product_name_id', $consumption->product_id)->first();
                $updated_qty = $stock_row->quantity + $consumption->consumption_qty;
                $upd_consumption_qty = $stock_row->consumption_qty - $consumption->consumption_qty;
                $stock_row->quantity = $updated_qty;
                $stock_row->consumption_qty = $upd_consumption_qty;
                $stock_row->save();
            }


            //update product_sales table
            ProductSale::where('invoice_no', $bill->invoice_no)->update(['invoice_no' => null, 'status' => 1]);

            //insert into customer_statements
            CustomerStatement::where('transaction_id', $bill->transaction_id)->delete();

            //INSERT INTO ENGINEER TIPS
            if ($bill->eng_tips > 0) {
                EngineerTipsStatement::where('transaction_id', $bill->transaction_id)->delete();
                Expense::where('transaction_id', $bill->transaction_id)->delete();
                CashStatement::where('transaction_id', $bill->transaction_id)->delete();
            }
//  Delete updated bill record
            DB::table('updated_bill')->where('bill_id', $bill->id)->delete();
            $bill->delete();

            DB::commit();
            Session::flash('message', 'Bill Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }


    public function editBill($id)
    {
        //    Log::info($id);
        try {
            $bill = Bill::select([
                'id',
                'bill_date',
                'concrete_method',
                'total_cft',
                'total_cuM',
                'total_amount',
                'pump_charge',
                'eng_tips',
                'vat',
                'ait',
                'description',
                'rate',
                'psi'
            ])->findOrFail($id);
//Log::info($bill);
            return response()->json([
                'success' => true,
                'bill' => $bill
            ]);

        } catch (\Exception $e) {

            //      Log::channel('single')->error('Bill Edit Error: '.$e->getMessage(), [
            //    'bill_id' => $id,
            //      'trace' => $e->getTraceAsString()
            //  ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load bill data'
            ], 500);
        }
    }

    public function updateBill(Request $request)
    {
        //    Log::info('updateBill method called');
//Log::info($request);
        $validated = $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'bill_date' => 'required|date',
            'concrete_method' => 'required|in:Pump,Direct,Manual',
            'total_cft' => 'required|numeric|min:0',
            'total_cuM' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'pump_charge' => 'nullable|numeric|min:0',
            'eng_tips' => 'nullable|numeric|min:0',
            'vat' => 'nullable|numeric|min:0|max:100',
            'ait' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:255',
            'returned_cft' => 'nullable|numeric|min:0',
            'returned_cum' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $bill = Bill::find($validated['bill_id']);

//Log::info($bill);

            if (!$bill) {
                //  Log::warning('Bill not found with ID: ' . $validated['bill_id']);
                return response()->json(['success' => false, 'message' => 'Bill not found'], 404);
            }

            $updateData = [
                'bill_date' => $validated['bill_date'],
                'concrete_method' => $validated['concrete_method'],
                'total_cft' => $validated['total_cft'] - ($validated['returned_cft']),
                'total_cuM' => $validated['total_cuM'] - ($validated['returned_cum']),
                'total_amount' => $validated['total_amount'] - ($validated['discount_amount']),
                'pump_charge' => $validated['pump_charge'] ?? 0,
                'eng_tips' => $validated['eng_tips'] ?? 0,
                'vat' => $validated['vat'] ?? 0,
                'ait' => $validated['ait'] ?? 0,
                'description' => $validated['description'] ?? null,
            ];
            // Log::info($bill);
            // Only update total_amount_before_discount if discount_amount is exactly 0


            $bill->update($updateData);


// insert into updated_bill table
            DB::table('updated_bill')->insert([
                'bill_id' => $validated['bill_id'],

                'returned_cft' => $validated['returned_cft'] ?? 0,
                'returned_cum' => $validated['returned_cum'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'created_at' => now(),

            ]);
// ekhane credit e tk store hocche tai balance update kora hoyni. karon onk statement hole sekhane majhkhaner statement er data change korle balance re calculation kora lagbe
            $transactionId = $bill->transaction_id;
            $customer_statements = CustomerStatement::where('transaction_id', $transactionId)->first();
            if ($customer_statements) {
                $customer_statements->credit = $bill->total_amount;
                $customer_statements->save();
            } else {
                Log::warning('error in generating bill: ' . $transactionId);
            }
            //   Log::info('Bill updated successfully', ['bill_id' => $bill->id, 'updated_fields' => $updateData]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bill updated successfully',
                'data' => $bill
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bill',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
