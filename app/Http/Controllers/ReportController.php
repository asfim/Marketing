<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\BankInstallmentInfo;
use App\Models\Branch;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\CashStatement;
use App\Models\BranchBalance;
use App\Models\BankInfo;
use App\Models\BankStatement;
use App\Models\Asset;
use App\Models\Customer;
use App\Models\ProductSale;
use App\Models\ProductPurchase;
use App\Models\ProductStock;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MixDesign;
use App\Models\CustomerStatement;
use App\Models\SupplierStatement;

class reportController extends Controller
{
    public function balanceReport()
    {
        $main_cash_bal = CashStatement::where('branchId',null)->sum(DB::raw('credit - debit'));
        $branches = Branch::all();

        $branch_result = "";
        $branch_totals = 0;
        foreach ($branches as $branch)
        {
            $_cash_balance = cashBalance($branch->id);
            $branch_result .= '<tr><td>'.$branch->name.'</td><td>BDT '.number_format($_cash_balance,2).'</td></tr>';
            $branch_totals += $_cash_balance;
        }

        $t_c_b = $main_cash_bal + $branch_totals;

        $cash_result = '<tr><td>*** MAIN BRANCH ***</td><td>BDT '.number_format($main_cash_bal,2).'</td></tr>'.$branch_result;
        $cash_result .= '<tr><td><b>Total = </b></td><td><b>BDT '.number_format($t_c_b,2).'</b></td></tr>';

        //bank balances
        $bank_infos = BankInfo::all();
        $bank_result = "";
        $total_bank_bal = 0;

        foreach ($bank_infos as $bank)
        {
            $balance = $bank->balance();
            $bank_result .= '<tr><td>'.$bank->bank_name.'</td><td>'.number_format($balance,2).'</td></tr>';
            $total_bank_bal += $balance;
        }
        $bank_result .= '<tr><td><b>Total = </b></td><td><b>BDT '.number_format($total_bank_bal,2).'</b></td></tr>';

        //asset value
        $asset_types = AssetType::all();
        $asset_result = "";
        $total_ass_val = 0;
        foreach ($asset_types as $asset_type)
        {
            $asset_value = $asset_type->asset_value();
            $asset_result .= '<tr><td>'.$asset_type->name.'</td><td>'.number_format($asset_value,2).'</td></tr>';
            $total_ass_val += $asset_value;
        }
//        $asset_result .= '<tr><td><b>Total = </b></td><td><b>BDT '.$total_ass_val.'</b></td></tr>';

        $all_total = $t_c_b + $total_bank_bal;

        return view('admin.report.report_balance',
            compact('cash_result','bank_result','asset_result','all_total','total_ass_val'));
    }

    public function profitReport()
    {
        //total sell
        $total_sell = DB::table('product_sales')
            ->join('mix_designs','product_sales.mix_design_id','=','mix_designs.id')
            ->sum(DB::raw('product_sales.cuM * 35.315 * mix_designs.rate'));

        //total income
        $total_income = Income::whereNotIn('income_name',['Customer Bill Receive'])->sum('amount');

        //investment
        $total_bank_invest = BankStatement::where('transaction_id','like','ADB-%')->sum('credit');
        $total_cash_invest = CashStatement::where('transaction_id','like','AC-%')->sum('credit');
        $total_invest = $total_bank_invest + $total_cash_invest;

        //present stock value
        $stock_rows = ProductStock::all();
        $total_stock_value=0;
        if($stock_rows != "")
        {
            foreach ($stock_rows as $row)
            {
                $_total= 0;
                if($row->product_name_id == 1)
                {
                    $_total = $row->quantity * 170;
                } elseif($row->product_name_id == 2) {
                    $_total = $row->quantity * 140;
                } elseif($row->product_name_id == 3) {
                    $_total = $row->quantity * 150;
                } elseif($row->product_name_id == 4) {
                    $_total = $row->quantity * 7300;
                } elseif($row->product_name_id == 5) {
                    $_total = ($row->quantity)/1000 * 6600;
                } elseif($row->product_name_id == 6) {
                    $_total = $row->quantity * 36;
                } elseif($row->product_name_id == 7) {
                    $_total = $row->quantity * 136;
                } elseif($row->product_name_id == 8) {
                    $_total = $row->quantity * 136;
                } elseif($row->product_name_id == 12) {
                    $_total = $row->quantity * 65;
                }

                $total_stock_value += $_total;
            }
        }

        //total purchase
        $total_purchase = ProductPurchase::sum('material_cost');

        //total expense
        $total_gen_expense= Expense::where('transaction_id','like','%GE%')->sum('amount');
        $total_pro_expense= Expense::where('transaction_id','like','%PE%')->sum('amount');
        $total_expense = $total_gen_expense + $total_pro_expense;

        //total Cash and bank balance
        //cash balances
        $main_cash_bal = CashStatement::sum(DB::raw('credit - debit'));
        $total_branch_bal= BranchBalance::sum('total_amount');
        $total_cash_bal= $main_cash_bal + $total_branch_bal;
        $total_bank_bal = BankStatement::sum(DB::raw('credit - debit'));
        $total_cash_bank_bal = $total_cash_bal + $total_bank_bal;

        //total supplier payble //bill adjustment //payment adjustment
        $total_supp_payble = SupplierStatement::sum(DB::raw('credit - debit'));
        $bill_adjustment = SupplierStatement::where('transaction_id', 'LIKE','%BILLAD%')->sum('debit');
        $payment_adj = SupplierPayment::sum('adjustment_amount');
        $total_adjustment = $bill_adjustment +$payment_adj;
        $supplier_payble = $total_supp_payble - $total_adjustment;

        //asset current value
        $current_asset_value = Asset::sum(DB::raw('purchase_amount-depreciated_amount'));
//
//        $dr = $total_sell + $total_income + $current_asset_value + $total_invest + $total_stock_value + $total_cash_bank_bal;
//        $cr = $total_purchase + $total_expense;
        $total_profit = ($total_sell + $total_income + $current_asset_value + $total_invest + $total_stock_value + $total_cash_bank_bal) - ($total_purchase + $total_expense + $supplier_payble);
        return view('admin.report.report_profit',  compact('total_sell','total_income','total_purchase','total_expense','total_profit','current_asset_value','total_invest','total_stock_value', 'total_cash_bank_bal', 'supplier_payble'));
    }

    public function overheadReport()
    {
        //total sell
        $challan_rows = ProductSale::all();
        $total_sell_qty = 0;

        if($challan_rows != "")
        {
            foreach ($challan_rows as $row_cha)
            {

                $total_sell_qty += $row_cha->cuM * 35.315;
            }
        }

        //total asset depreciation
        $asset_rows = Asset::all();
        $total_asset_depreciation = 0;

        if($asset_rows != "")
        {
            foreach ($asset_rows as $row)
            {
                $total_asset_depreciation += $row->depreciation;
            }
        }


        //total expense Gen ex without eng tips + Rent + Installment
        $expense_rows = Expense::whereNotIn('expense_type_id',['27,9,24'])->pluck('amount');
        $total_expense = 0;

        if($expense_rows != "")
        {
            foreach ($expense_rows as $row_ex)
            {
                $total_expense += $row_ex;
            }
        }


        $total_over_head_cost = ($total_expense + $total_asset_depreciation) / $total_sell_qty;
        return view('admin.report.report_overhead',  compact('total_expense','total_asset_depreciation','total_sell_qty','total_over_head_cost'));
    }

    public function expenseReport(Request $request)
    {
        $user = Auth::user();
        $branches = Branch::orderBy('name')->get();
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        }elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $branchId = $request->branchId??$user->branchId;

        $expenses = Expense::query();


        if($branchId == 'head_office'){
            $expenses = $expenses->where('branchId', null);
        } elseif ($branchId != ''){
            $expenses = $expenses->where('branchId', $branchId);
        }

        if(isset($date_range)){
            $expenses = $expenses->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($request->search_text != ''){
            $expenses = $expenses->where('expense_name', $request->search_text)->orWhereHas('expense_type',function($query) use($request) {
                $query->where('type_name','like','%'.$request->search_text.'%');
            });
        }

        $expenses = $expenses->orderBy('date','desc')->get();

        return view('admin.report.report_expense',compact('expenses','branches'));
    }


    public function incomeReport(Request $request)
    {
        $user = Auth::user();
        $branches = Branch::orderBy('name')->get();
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $branchId = $request->branchId??$user->branchId;

        $incomes = Income::query();

        if($branchId == 'head_office'){
            $incomes = $incomes->where('branchId', null);
        } elseif ($branchId != ''){
            $incomes = $incomes->where('branchId', $branchId);
        }

        if(isset($date_range)){
            $incomes = $incomes->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($request->search_text != ''){
            $incomes = $incomes->where('income_name', $request->search_text)->orWhereHas('income_type',function($query) use($request) {
                $query->where('type_name','like','%'.$request->search_text.'%');
            });
        }

        $incomes = $incomes->orderBy('date','desc')->get();

        return view('admin.report.report_income',compact('incomes','branches'));
    }

    public function investmentReport(Request $request)
    {
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }else{
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];

        }

        $bank_investments = BankStatement::where('transaction_id','like','%ADB%');
        $cash_investments = CashStatement::where('transaction_id','like','%AC%');

        if(isset($date_range)){
            $bank_investments = $bank_investments->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $cash_investments = $cash_investments->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }


        $bank_investments = $bank_investments->orderBy('id','DESC')->get();
        $cash_investments = $cash_investments->orderBy('id','DESC')->get();

        return view('admin.report.report_investment',compact('bank_investments','cash_investments'));
    }
// public function allCustomerBalanceReport(Request $request)
// {
//     $date_info = "";
//     $search_name = $request->input('search_name');
//     $from_date   = $request->input('from_date');
//     $to_date     = $request->input('to_date');

//     // Default date range: last 30 days
//     $from = $from_date ?? date('Y-m-d', strtotime('-30 days'));
//     $to   = $to_date   ?? date('Y-m-d');
    
  

//     if ($from_date && $to_date) {
//         $request->validate([
//             'from_date' => 'required|date',
//             'to_date'   => 'required|date|after_or_equal:from_date',
//         ]);

//         $date_info = "Showing results From " . date('d-m-Y', strtotime($from))
//                    . " To " . date('d-m-Y', strtotime($to));
//     }
//     $customer_statements = Customer::query();
    
    

//     if($search_name){
//         $customer_statements->where('name','LIKE','%'.$search_name.'%');
//     }

//     if ($from_date && $to_date) {
//     $customer_statements->whereBetween('created_at', [$from_date, $to_date]);
//     }

//     if($search_name && $from_date && $to_date){
//         $customer_statements->where('name','LIKE','%'.$search_name.'%');
//         $customer_statements->whereBetween('created_at', [$from_date, $to_date]);
//     }


//     $customer_statements = $customer_statements->get();
  
    

//     // Totals
//     $total_debit = $total_credit = $total_billable_all = $total_balance = $total_new_balance = 0;

//    return view('admin.report.report_customer_balance', compact('customer_statements', 'date_info', 'total_debit', 'total_credit', 'total_new_balance'));

// }

public function allCustomerBalanceReport(Request $request)
{
    $date_info = "";
    $search_name = $request->input('search_name');
    $from_date   = $request->input('from_date');
    $to_date     = $request->input('to_date');

    // Default date range: last 30 days
    $from = $from_date ?? date('Y-m-d', strtotime('-30 days'));
    $to   = $to_date   ?? date('Y-m-d');

    // Validate date range if both provided
    if ($from_date && $to_date) {
        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $date_info = "Showing results From " . date('d-m-Y', strtotime($from))
                   . " To " . date('d-m-Y', strtotime($to));
    }

    // Base query
    $customer_statements = Customer::query();

    if ($search_name) {
        $customer_statements->where('name', 'LIKE', '%' . $search_name . '%');
    }

    // Get all customers (we filter their balance inside Blade using balanceText)
    $customer_statements = $customer_statements->get();

    // Totals placeholders (you can calculate later if needed)
    $total_debit = $total_credit = $total_billable_all = $total_balance = $total_new_balance = 0;

    return view('admin.report.report_customer_balance', compact(
        'customer_statements',
        'date_info',
        'total_debit',
        'total_credit',
        'total_new_balance',
        'from', 
        'to'
    ));
}



    public function allSupplierBalanceReport(Request $request)
    {
        $all_statements = "";
        $date_info = "";
        $supObj  = new SupplierStatement();

        if($request->input('search_name') != "" || ($request->input('from_date') != "" && $request->input('to_date') != ""))  {

            $from_date  = date("Y-m-d", strtotime($request->input('from_date')));
            $to_date    = date("Y-m-d", strtotime($request->input('to_date')));
            $text = $request->input('search_name');


            //if search between date
            if($request->input('search_name') == "" && $request->input('from_date') != "" && $request->input('to_date') != "")
            {

                $this->validate($request, [
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after:from_date'
                ]);

                //$all_statements = $custObj->whereBetween('posting_date', [$from_date, $to_date])->get();
                $all_statements = DB::select('SELECT c.name,SUM(cs.debit) as payable ,SUM(cs.credit) as receivable,(SUM(cs.credit) - SUM(cs.debit)) as balance from supplier_statements as cs '
                    . 'JOIN suppliers as c ON c.id = cs.supplier_id WHERE cs.posting_date >= ? AND cs.posting_date <= ? GROUP BY c.name,cs.supplier_id ORDER BY c.name ASC',[$from_date, $to_date]);

                //search date info
                $f_date = date('d-m-Y', strtotime($from_date));
                $t_date = date('d-m-Y',strtotime($to_date));
                $date_info = "Showing results From ".$f_date." To ".$t_date;


            }

            //if name and between date search
            elseif($request->input('search_name') != "" && $request->input('from_date') != "" && $request->input('to_date') != "")
            {

                $this->validate($request, [
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after:from_date'
                ]);
                //search date info
                $f_date = date('d-m-Y', strtotime($from_date));
                $t_date = date('d-m-Y',strtotime($to_date));
                $date_info = "Showing results From ".$f_date." To ".$t_date;

                $all_statements = DB::select('SELECT c.name,SUM(cs.debit) as payable ,SUM(cs.credit) as receivable,(SUM(cs.credit) - SUM(cs.debit)) as balance from supplier_statements as cs '
                    . 'JOIN suppliers as c ON c.id = cs.supplier_id WHERE cs.posting_date >= ? AND cs.posting_date <= ? AND c.name = ? GROUP BY c.name,cs.supplier_id ORDER BY c.name ASC',[$from_date, $to_date, $text]);

            }

            elseif($request->input('search_name') != "" && $request->input('from_date') == "" && $request->input('to_date') == "")
            {


                //search date info
                $date_info = "";

                $all_statements = DB::select('SELECT c.name,SUM(cs.debit) as payable ,SUM(cs.credit) as receivable,(SUM(cs.credit) - SUM(cs.debit)) as balance from supplier_statements as cs '
                    . 'JOIN suppliers as c ON c.id = cs.supplier_id WHERE c.name = ? GROUP BY c.name,cs.supplier_id ORDER BY c.name ASC',[$text]);

            }

        } else {
            $today = date('Y-m-d');
            $last_month = date('Y-m-d', strtotime('today - 30 days'));

            $all_statements = DB::select('SELECT c.name,SUM(cs.debit) as payable ,SUM(cs.credit) as receivable,(SUM(cs.credit) - SUM(cs.debit)) as balance '
                . 'from supplier_statements as cs JOIN suppliers as c ON c.id = cs.supplier_id GROUP BY c.name,cs.supplier_id ORDER BY c.name ASC');
            //$all_statements = CustomerStatement::whereBetween('posting_date',[$last_month,$today])->get();
          
            $date_info = "";
        }//else end
        // format balance before sending to view
            foreach ($all_statements as $row) {

                $balance_raw = $row->balance;

            if ($balance_raw < 0) {

                $row->balance_text = '<span style="background:#007bff;color:#fff;padding:3px 6px;border-radius:4px;">'
                                    . number_format(abs($balance_raw), 2) . ' TK Advance</span>';
            } elseif ($balance_raw > 0) {

                $row->balance_text = '<span style="background:#dc3545;color:#fff;padding:3px 6px;border-radius:4px;">'
                                    . number_format($balance_raw, 2) . ' TK Due</span>';
            } else {
 
                $row->balance_text = '<span style="background:#6c757d;color:#fff;padding:3px 6px;border-radius:4px;">0 TK</span>';
            }

            }


        return view('admin.report.report_supplier_balance',compact('all_statements','date_info'));
    }

    public function activityLog(Request $request)
    {
        $users = User::all();
        $activity_logs = Activity::orderBy('id','desc');
        $tables = Activity::groupBy('log_name')->pluck('log_name');
        $activity_logs = $activity_logs->get();

        return view('admin_panel.pages.activity-log.activity-log',compact('users','tables','activity_logs'));
    }


    public function PLReport(Request $request)
    {
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } else{
            $date_range = [date('Y-m-d',strtotime('-1 year')), date('Y-m-d')];
        }

        $product_sale = DB::table('product_sales')
            ->select(DB::raw('sum(product_sales.cuM * 35.315 * mix_designs.rate) as total_sale'))
            ->join('mix_designs','product_sales.mix_design_id','=','mix_designs.id');

        $product_purchase = ProductPurchase::query();

        $general_expenses = DB::table('expenses')
            ->select(DB::raw('expense_types.type_name,sum(expenses.amount) as total_expense'))
            ->join('expense_types','expenses.expense_type_id','=','expense_types.id')
            ->groupBy('expense_types.type_name');
        $production_expenses = DB::table('expenses')
            ->select(DB::raw('expense_types.type_name,sum(expenses.amount) as total_expense'))
            ->join('expense_types','expenses.expense_type_id','=','expense_types.id')
            ->groupBy('expense_types.type_name');

        if(isset($date_range)) {
            $product_sale = $product_sale->whereBetween('product_sales.sell_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $product_purchase = $product_purchase->whereBetween('purchase_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $general_expenses = $general_expenses->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $production_expenses = $production_expenses->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        $product_sale = $product_sale->first();
        $product_purchase = $product_purchase->selectRaw('sum(material_cost) as material_cost')->first();

        $general_expenses = $general_expenses->where('transaction_id','like','%GE%')->get();
        $production_expenses = $production_expenses->where('transaction_id','like','%PE%')->get();

        $data['total_sale'] = $product_sale->total_sale??0;
        $data['raw_material_purchase'] = $product_purchase->material_cost??0;
        $data['production_expenses'] = $production_expenses;
        $data['general_expenses'] = $general_expenses;
        $data['total_production_expense'] = $production_expenses->sum('total_expense');
        $data['total_general_expense'] = $general_expenses->sum('total_expense');

        return view('admin.report.report_profit_loss',  compact('data'));
    }

    public function balanceSheet(Request $request)
    {
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } else{
            $date_range = [date('Y-m-d',strtotime('-1 year')), date('Y-m-d')];
        }

        $fixed_asset = Asset::query();
        $cash_balance = CashStatement::query();
        $bank_balance = BankStatement::query();
        $ac_receivable = CustomerStatement::query();
        $ac_payable = SupplierStatement::query();

        if(isset($date_range)) {
            $fixed_asset = $fixed_asset->whereBetween('purchase_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $cash_balance = $cash_balance->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $bank_balance = $bank_balance->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $ac_receivable = $ac_receivable->whereBetween('posting_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $ac_payable = $ac_payable->whereBetween('posting_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        $fixed_asset = $fixed_asset->sum('purchase_amount');
        $cash_balance = $cash_balance->sum(DB::raw('credit - debit'));
        $bank_balance = $bank_balance->sum(DB::raw('credit - debit'));
        $ac_receivable = $ac_receivable->sum(DB::raw('credit - debit'));
        $ac_payable = $ac_payable->sum(DB::raw('credit - debit'));


        //present stock value
        $stock_rows = ProductStock::all();
        $current_stock_value=0;
        if($stock_rows != "")
        {
            foreach ($stock_rows as $row)
            {
                $_total = $row->quantity * $row->product_name->unit_price;
                $current_stock_value += $_total;
            }
        }

        $total_loan = BankInstallmentInfo::sum(DB::raw('total_loan-total_loan_paid'));


        //START OF PROFIT & LOSS REPORT
        $product_sale = DB::table('product_sales')
            ->select(DB::raw('sum(product_sales.cuM * 35.315 * mix_designs.rate) as total_sale'))
            ->join('mix_designs','product_sales.mix_design_id','=','mix_designs.id');

        $product_purchase = ProductPurchase::query();

        $general_expenses = DB::table('expenses')
            ->select(DB::raw('expense_types.type_name,sum(expenses.amount) as total_expense'))
            ->join('expense_types','expenses.expense_type_id','=','expense_types.id')
            ->groupBy('expense_types.type_name');
        $production_expenses = DB::table('expenses')
            ->select(DB::raw('expense_types.type_name,sum(expenses.amount) as total_expense'))
            ->join('expense_types','expenses.expense_type_id','=','expense_types.id')
            ->groupBy('expense_types.type_name');

        if(isset($date_range)) {
            $product_sale = $product_sale->whereBetween('product_sales.sell_date', $date_range);
            $product_purchase = $product_purchase->whereBetween('purchase_date', $date_range);
            $general_expenses = $general_expenses->whereBetween('date', $date_range);
            $production_expenses = $production_expenses->whereBetween('date', $date_range);
        }

        $product_sale = $product_sale->first();
        $product_purchase = $product_purchase->selectRaw('sum(material_cost) as material_cost')->first();

        $general_expenses = $general_expenses->where('transaction_id','like','%GE%')->get();
        $production_expenses = $production_expenses->where('transaction_id','like','%PE%')->get();

        $data['total_sale'] = $product_sale->total_sale??0;
        $data['raw_material_purchase'] = $product_purchase->material_cost??0;
        $data['total_production_expense'] = $production_expenses->sum('total_expense');
        $data['total_general_expense'] = $general_expenses->sum('total_expense');
        $total_profit = $data['total_sale']-$data['raw_material_purchase']-$data['total_production_expense']-$data['total_general_expense'];
        //END OF PROFIT & LOSS REPORT

        $data['fixed_asset'] = $fixed_asset;
        $data['cash_balance'] = $cash_balance;
        $data['bank_balance'] = $bank_balance;
        $data['ac_receivable'] = $ac_receivable;
        $data['current_stock_value'] = $current_stock_value;
        $data['total_loan'] = $total_loan;
        $data['ac_payable'] = $ac_payable;
        $data['total_profit'] = $total_profit;

//        echo "<pre>";
//        print_r($data);die;

        return view('admin.report.report_balance_sheet',  compact('data'));
    }

    public function trialBalance(Request $request)
    {
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        }

        $main_cash_balance = DB::table('cash_statements')->select(DB::raw('sum(debit) as debit, sum(credit) as credit'))
            ->where('branchId',null);

        $branch_balances = DB::table('cash_statements')->select(DB::raw('name, sum(debit) as debit, sum(credit) as credit'))
            ->join('branches','cash_statements.branchId','branches.id');

        $bank_balances = DB::table('bank_statements')->select(DB::raw('bank_name, sum(debit) as debit, sum(credit) as credit'))
            ->join('bank_infos','bank_statements.bank_info_id','bank_infos.id')
            ->where('bank_infos.account_type','!=','Loan');

        $ac_receivable = DB::table('customer_statements')->select(DB::raw('customers.name, sum(debit) as debit, sum(credit) as credit'))
            ->join('customers','customer_statements.customer_id','customers.id')
            ->groupBy('customers.name')->get();

        $ac_payable = DB::table('supplier_statements')->select(DB::raw('suppliers.name, sum(debit) as debit, sum(credit) as credit'))
            ->join('suppliers','supplier_statements.supplier_id','suppliers.id')
            ->groupBy('suppliers.name')->get();

        //PRESENT ASSET VALUE
        $asset_types = AssetType::all();

        //PRESENT STOCK VALUE
        $product_stocks = ProductStock::all();

        /* EXPENSES */
        $general_expenses = DB::table('expenses')
            ->select(DB::raw('expense_types.type_name,sum(expenses.amount) as total_expense'))
            ->join('expense_types','expenses.expense_type_id','=','expense_types.id')
            ->where('transaction_id','like','%GE%')
            ->groupBy('expense_types.type_name');
        $production_expenses = DB::table('expenses')
            ->select(DB::raw('expense_types.type_name,sum(expenses.amount) as total_expense'))
            ->join('expense_types','expenses.expense_type_id','=','expense_types.id')
            ->where('transaction_id','like','%PE%')
            ->groupBy('expense_types.type_name');

        /* INCOMES */
        $general_incomes = DB::table('incomes')
            ->select(DB::raw('income_types.type_name,sum(incomes.amount) as total_income'))
            ->join('income_types','incomes.income_type_id','=','income_types.id')
            ->where('transaction_id','like','%GI%')
            ->groupBy('income_types.type_name');
        $waste_incomes = DB::table('incomes')
            ->select(DB::raw('income_types.type_name,sum(incomes.amount) as total_income'))
            ->join('income_types','incomes.income_type_id','=','income_types.id')
            ->where('transaction_id','like','%WI%')
            ->groupBy('income_types.type_name');

        /* BANK LOANS */
        $bank_loans = DB::table('bank_installment_infos')
            ->select(DB::raw('bank_infos.bank_name,sum(bank_installment_infos.total_loan) as total_loan,sum(bank_installment_infos.total_loan_paid) as total_loan_paid'))
            ->join('bank_infos','bank_installment_infos.bank_id','=','bank_infos.id')
            ->groupBy('bank_infos.bank_name')->get();

        /* PRODUCT/ RAW MATERIAL PURCHASE */
        $product_purchases = DB::table('product_purchases')
            ->select(DB::raw('product_names.name,sum(product_purchases.material_cost) as total_material_cost'))
            ->join('product_names','product_purchases.product_name_id','=','product_names.id')
            ->groupBy('product_names.name');


        if(isset($date_range)) {
            $main_cash_balance = $main_cash_balance->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $branch_balances = $branch_balances->whereBetween('cash_statements.ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $bank_balances = $bank_balances->whereBetween('bank_statements.ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $general_expenses = $general_expenses->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $production_expenses = $production_expenses->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $general_incomes = $general_incomes->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $waste_incomes = $waste_incomes->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
            $product_purchases = $product_purchases->whereBetween('purchase_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        $main_cash_balance = $main_cash_balance->first();
        $branch_balances = $branch_balances->groupBy('name')->get();
        $bank_balances = $bank_balances->groupBy('bank_name')->get();
        $general_expenses = $general_expenses->get();
        $production_expenses = $production_expenses->get();
        $general_incomes = $general_incomes->get();
        $waste_incomes = $waste_incomes->get();
        $product_purchases = $product_purchases->get();

        $data['main_cash_balance'] = $main_cash_balance;
        $data['branch_balances'] = $branch_balances;
        $data['bank_balances'] = $bank_balances;
        $data['ac_receivable'] = $ac_receivable;
        $data['ac_payable'] = $ac_payable;
        $data['asset_types'] = $asset_types;
        $data['product_stocks'] = $product_stocks;
        $data['general_expenses'] = $general_expenses;
        $data['production_expenses'] = $production_expenses;
        $data['general_incomes'] = $general_incomes;
        $data['waste_incomes'] = $waste_incomes;
        $data['product_purchases'] = $product_purchases;
        $data['bank_loans'] = $bank_loans;

        return view('admin.report.report_trial_balance',  compact('data'));
    }



// public function quickReport()
//  {
//     $user = Auth::user();
//     $isSuperAdmin = $user && $user->is_super_admin;
//     $branchId = $user ? $user->branchId : null;

//     /** Purchase List - Grouped by Month (Last 3 Months) */
//     $threeMonthsAgo = now()->subMonths(3)->format('Y-m-d H:i:s');

//     $purchaseSummaryQuery = DB::table('product_purchases')
//         ->selectRaw('COUNT(*) AS purchase_count, YEAR(purchase_date) AS Year, MONTH(purchase_date) AS Month, SUM(material_cost) AS total_mcost')
//         ->where('purchase_date', '>=', $threeMonthsAgo);

//     if (!$isSuperAdmin && $branchId) {
//         $purchaseSummaryQuery->where('branchId', $branchId);
//     }

//     $purchaseSummaryQuery->groupBy(DB::raw('YEAR(purchase_date), MONTH(purchase_date)'))
//         ->orderByRaw('YEAR(purchase_date) DESC, MONTH(purchase_date) DESC');

//     $total_purchase_month = $purchaseSummaryQuery->get();
//     $total_purchase_month_array = [];

//     if ($total_purchase_month->isNotEmpty()) {
//         $tpm = 0;
//         $pmonth = '';
//         $pp = 0;

//         foreach ($total_purchase_month as $tp_month) {
//             // Sub-query: count per supplier + product
//             $detailQuery = DB::table('product_purchases')
//                 ->selectRaw('MAX(id) as mid, supplier_id, product_name_id, SUM(material_cost) as tm_cost, COUNT(*) as tp_count')
//                 ->whereYear('purchase_date', $tp_month->Year)
//                 ->whereMonth('purchase_date', $tp_month->Month);

//             if (!$isSuperAdmin && $branchId) {
//                 $detailQuery->where('branchId', $branchId);
//             }

//             $detailQuery->groupBy('supplier_id', 'product_name_id')
//                 ->orderBy('supplier_id')
//                 ->orderBy('product_name_id')
//                 ->orderByRaw('MAX(id) DESC');

//             $query_count_data = $detailQuery->get();

//             $query_count_data_array = [];
//             foreach ($query_count_data as $item) {
//                 $key = $item->supplier_id . '_' . $item->product_name_id;
//                 $query_count_data_array[$key] = $item->tp_count . '___' . $item->tm_cost;
//             }

//             // Final detailed purchase data
//             $finalQuery = DB::table('product_purchases')
//                 ->join('suppliers', 'product_purchases.supplier_id', '=', 'suppliers.id')
//                 ->join('product_names', 'product_purchases.product_name_id', '=', 'product_names.id')
//                 ->select('product_purchases.chalan_no','product_purchases.supplier_id','product_purchases.product_name_id','product_purchases.purchase_date',
//                     'product_purchases.unit_type',
//                     'product_purchases.product_qty',
//                     'product_purchases.rate_per_unit',
//                     'product_purchases.material_cost',
//                     'product_purchases.total_material_cost',
//                     'suppliers.name as sname',
//                     'product_names.name as pname'
//                 )
//                 ->whereYear('product_purchases.purchase_date', $tp_month->Year)
//                 ->whereMonth('product_purchases.purchase_date', $tp_month->Month);
                

//             if (!$isSuperAdmin && $branchId) {
//                 $finalQuery->where('product_purchases.branchId', $branchId);
//             }

//             $finalQuery->orderBy('product_purchases.supplier_id')
//                 ->orderBy('product_purchases.product_name_id')
//                 ->orderBy('product_purchases.id', 'DESC');

//             $purchase_data = $finalQuery->get();

            

//             $purchase_data_array = '';
//             if ($purchase_data->isNotEmpty()) {
//                 $last_id = '';

//                 foreach ($purchase_data as $index => $purchase) {
//                     if ($pmonth == '') {
//                         $pmonth = $tp_month->Month;
//                     }

//                     if ($pmonth == $tp_month->Month) {
//                         $tpm++;
//                         $tpmk = $tpm;
//                     } else {
//                         $tpmk = 1;
//                         $tpm = 0;
//                         $pmonth = '';
//                     }

//                     $sup_id = $purchase->supplier_id . '_' . $purchase->product_name_id;
//                     $count_tm_cost = $query_count_data_array[$sup_id] ?? '0___0';
//                     [$count, $tmc_cost] = explode('___', $count_tm_cost);

//                     $purchase_data_array .= '<tr>
//                         <td><div style="width: 65px;">' . e($purchase->purchase_date) . '</div></td>
//                         <td>' . e($purchase->chalan_no) . '</td>
                    
//                         <td>' . e($purchase->sname) . '</td>
//                         <td>' . e($purchase->pname) . '</td>
//                         <td>' . round($purchase->product_qty, 3) . '</td>
//                         <td>' . e($purchase->unit_type) . '</td>
//                         <td>' . round($purchase->rate_per_unit, 3) . '</td>
//                         <td>' . round($purchase->material_cost, 3) . '</td>';

//                     if ($last_id != $sup_id) {
//                         $purchase_data_array .= '<td rowspan="' . e($count) . '">' . round($tmc_cost, 3) . '</td>';
//                     }

//                     if ($tpmk == 1) {
//                         $rowspan = $tp_month->purchase_count > 1 ? 'rowspan="' . $tp_month->purchase_count . '"' : '';
//                         $purchase_data_array .= '<td ' . $rowspan . '>' . round($tp_month->total_mcost, 3) . '</td>';
//                     }

//                     $purchase_data_array .= '</tr>';
//                     $last_id = $sup_id;
//                 }
//             }

//             $total_purchase_month_array[] = '<h3>' . date("F Y", mktime(0, 0, 0, $tp_month->Month, 1, $tp_month->Year)) . '</h3>
//                 <div>
//                     <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
//                         <thead>
//                             <tr>
//                                 <th><div style="width: 65px;">Date</div></th>
//                                 <th>C.No</th>
//                                 <th>S.Name</th>
//                                 <th>P.Name</th>
//                                 <th>Qty</th>
//                                 <th>U.Type</th>
//                                 <th>RPU</th>
//                                 <th>M.Cost</th>
//                                 <th>T.M.Cost</th>
//                                 <th>G.T.M.Cost</th>
//                             </tr>
//                         </thead>
//                         <tbody>' . $purchase_data_array . '</tbody>
//                     </table>
//                 </div>';

//             $pp++;
//         }
//     }

//     /********************** SECOND SECTION: Aggregated Purchase List **********************/
//    // 🔹 PURCHASE LIST WITH BRANCH NAME
// $purchaseListQuery = DB::table('product_purchases as pp')
//     ->join('product_names as p', 'p.id', '=', 'pp.product_name_id')
//     ->join('branches', 'branches.id', '=', 'pp.branchId')
//     ->selectRaw('
//         pp.product_name_id,
//         p.name,
//         branches.name as branch_name, 
//         MONTH(pp.received_date) as month,
//         YEAR(pp.received_date) as year,
//         pp.unit_type,
//         SUM(pp.product_qty) as total_qty,
//         SUM(pp.total_material_cost) as total_mat_cost
//     ')
//     ->groupBy(DB::raw('
//         MONTH(pp.received_date),
//         YEAR(pp.received_date),
//         p.name,
//         pp.product_name_id,
//         pp.unit_type,
//         branches.name 
//     '))
//     ->orderBy('pp.received_date', 'DESC');

// if (!$isSuperAdmin && $branchId) {
//     $purchaseListQuery->where('pp.branchId', $branchId);
// }
// $purchase_list = $purchaseListQuery->get();

// // 🔹 MONTHS LIST (no branch needed here)
// $monthsListQuery = DB::table('product_purchases as pp')
//     ->selectRaw('MONTH(pp.received_date) as month, YEAR(pp.received_date) as year, COUNT(pp.received_date) as row_total')
//     ->groupBy(DB::raw('MONTH(pp.received_date), YEAR(pp.received_date)'))
//     ->orderBy('year', 'DESC')
//     ->orderBy('month', 'DESC');

// if (!$isSuperAdmin && $branchId) {
//     $monthsListQuery->where('pp.branchId', $branchId);
// }
// $months_list = $monthsListQuery->get();

// // 🔹 BUILD HTML WITH BRANCH NAME
// $html_purchase_list = [];
// $i = 0;
// foreach ($months_list as $pmlist) {
//     $html_purchase_list[$i] = '<h3>' . date("F Y", mktime(0, 0, 0, $pmlist->month, 1, $pmlist->year)) . '</h3>
//         <div>
//             <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
//                 <thead>
//                     <tr>
//                         <th width="5%">SN</th>
//                         <th width="20%">Material Name</th>
//                         <th width="20%">Branch</th> 
//                         <th width="20%">Total qty</th>
//                         <th width="20%">Total Mat Cost</th>
//                     </tr>
//                 </thead>
//                 <tbody>';

//     $k = 0;
//     foreach ($purchase_list as $list) {
//         if ($pmlist->month == $list->month && $pmlist->year == $list->year) {
//             $k++;
//             $html_purchase_list[$i] .= '<tr>
//                 <td>' . $k . '</td>
//                 <td>' . e($list->name) . '</td>
//                 <td>' . e($list->branch_name) . '</td> 
//                 <td>' . round($list->total_qty, 2) . ' ' . e($list->unit_type) . '</td>
//                 <td>' . round($list->total_mat_cost, 2) . '</td>
//             </tr>';
//         }
//     }

//     $html_purchase_list[$i] .= '</tbody></table></div>';
//     $i++;
//     if ($i >= 2) break; // Only show last 2 months
// }

   
// // 🔹 SALES LIST WITH BRANCH NAME
// $salesListQuery = DB::table('product_sales as ps')
//     ->join('mix_designs', 'mix_designs.id', '=', 'ps.mix_design_id')
//     ->join('branches', 'branches.id', '=', 'ps.branchId') 
//     ->selectRaw('mix_designs.rate, mix_designs.psi, ps.sell_date, SUM(ps.cuM) as total_cum, branches.name as branch_name')
//     ->groupBy('ps.sell_date', 'mix_designs.psi', 'mix_designs.rate', 'branches.name') 
//     ->orderBy('ps.sell_date', 'DESC');

// if (!$isSuperAdmin && $branchId) {
//     $salesListQuery->where('ps.branchId', $branchId);
// }
// $sales_list = $salesListQuery->get();

// // 🔹 SALES MONTHS SUMMARY (no branch name needed here)
// $salesMonthsQuery = DB::table('product_sales as ps')
//     ->selectRaw('MONTH(ps.sell_date) as month, YEAR(ps.sell_date) as year, COUNT(ps.sell_date) as row_total, SUM(ps.cuM) as total_cum')
//     ->groupBy(DB::raw('MONTH(ps.sell_date), YEAR(ps.sell_date)'))
//     ->orderBy('year', 'DESC')
//     ->orderBy('month', 'DESC');

// if (!$isSuperAdmin && $branchId) {
//     $salesMonthsQuery->where('ps.branchId', $branchId);
// }
// $sales_months_list = $salesMonthsQuery->get();

// // 🔹 BUILD HTML WITH BRANCH NAME
// $html_sales_list = [];
// $i = 0;
// foreach ($sales_months_list as $smlist) {
//     $html_sales_list[$i] = '<h3>' .
//         date("F Y", mktime(0, 0, 0, $smlist->month, 1, $smlist->year)) .
//         '<span style="float:right;margin-right:25px;">Total: ' . round($smlist->total_cum, 2) . '</span>
//         </h3>
//         <div>
//             <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
//                 <thead>
//                     <tr>
//                         <th width="15%">Date</th>
//                         <th width="15%">Branch</th> 
//                         <th width="15%">PSI</th>
//                         <th width="15%">Total cuM</th>
//                         <th width="20%">Total</th>
//                     </tr>
//                 </thead>
//                 <tbody>';

//     foreach ($sales_list as $list) {
//         $sellMonth = (int) date('m', strtotime($list->sell_date));
//         $sellYear = (int) date('Y', strtotime($list->sell_date));

//         if ($smlist->month == $sellMonth && $smlist->year == $sellYear) {
//             $totalValue = $list->total_cum * 35.15 * $list->rate;
//             $html_sales_list[$i] .= '<tr>
//                 <td>' . date('d-M-y', strtotime($list->sell_date)) . '</td>
//                 <td>' . e($list->branch_name) . '</td> 
//                 <td>' . e($list->psi) . '</td>
//                 <td>' . round($list->total_cum, 2) . '</td>
//                 <td>' . number_format($totalValue, 2) . '</td>
//             </tr>';
//         }
//     }

//     $html_sales_list[$i] .= '</tbody></table></div>';
//     $i++;
//     if ($i >= 2) break; // Only last 2 months
// }
//     /********************** PRODUCT STOCK (NO FILTER) **********************/
//     $product_stocks = ProductStock::all();
//      // No filtering as per requirement
  
//     return view('admin.report.report_quick', compact(
//         'product_stocks',
//         'html_purchase_list',
//         'html_sales_list'
        
//     ));
// }




    public function quickReport()
{
    $user = Auth::user();
    $isSuperAdmin = $user && $user->is_super_admin;
    $branchId = $user ? $user->branchId : null;
    
    // 🔹 বিশেষ ইউজার যারা সব ব্রাঞ্চ দেখতে পারবে (যেমন user_id = 17)
    $canViewAllBranches = $user && $user->id == 17; // user_id 17 সব ব্রাঞ্চ দেখবে

    /** Purchase List - Grouped by Month (Last 3 Months) */
    $threeMonthsAgo = now()->subMonths(3)->format('Y-m-d H:i:s');

    $purchaseSummaryQuery = DB::table('product_purchases')
        ->selectRaw('COUNT(*) AS purchase_count, YEAR(purchase_date) AS Year, MONTH(purchase_date) AS Month, SUM(material_cost) AS total_mcost')
        ->where('purchase_date', '>=', $threeMonthsAgo);

    // ব্রাঞ্চ ফিল্টারিং: সুপার অ্যাডমিন অথবা user_id=17 না হলে ব্রাঞ্চ ফিল্টার
    if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
        $purchaseSummaryQuery->where('branchId', $branchId);
    }

    $purchaseSummaryQuery->groupBy(DB::raw('YEAR(purchase_date), MONTH(purchase_date)'))
        ->orderByRaw('YEAR(purchase_date) DESC, MONTH(purchase_date) DESC');

    $total_purchase_month = $purchaseSummaryQuery->get();
    $total_purchase_month_array = [];

    if ($total_purchase_month->isNotEmpty()) {
        $tpm = 0;
        $pmonth = '';
        $pp = 0;

        foreach ($total_purchase_month as $tp_month) {
            // Sub-query: count per supplier + product
            $detailQuery = DB::table('product_purchases')
                ->selectRaw('MAX(id) as mid, supplier_id, product_name_id, SUM(material_cost) as tm_cost, COUNT(*) as tp_count')
                ->whereYear('purchase_date', $tp_month->Year)
                ->whereMonth('purchase_date', $tp_month->Month);

            if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
                $detailQuery->where('branchId', $branchId);
            }

            $detailQuery->groupBy('supplier_id', 'product_name_id')
                ->orderBy('supplier_id')
                ->orderBy('product_name_id')
                ->orderByRaw('MAX(id) DESC');

            $query_count_data = $detailQuery->get();

            $query_count_data_array = [];
            foreach ($query_count_data as $item) {
                $key = $item->supplier_id . '_' . $item->product_name_id;
                $query_count_data_array[$key] = $item->tp_count . '___' . $item->tm_cost;
            }

            // Final detailed purchase data
            $finalQuery = DB::table('product_purchases')
                ->join('suppliers', 'product_purchases.supplier_id', '=', 'suppliers.id')
                ->join('product_names', 'product_purchases.product_name_id', '=', 'product_names.id')
                ->select('product_purchases.chalan_no','product_purchases.supplier_id','product_purchases.product_name_id','product_purchases.purchase_date',
                    'product_purchases.unit_type',
                    'product_purchases.product_qty',
                    'product_purchases.rate_per_unit',
                    'product_purchases.material_cost',
                    'product_purchases.total_material_cost',
                    'suppliers.name as sname',
                    'product_names.name as pname'
                )
                ->whereYear('product_purchases.purchase_date', $tp_month->Year)
                ->whereMonth('product_purchases.purchase_date', $tp_month->Month);

            if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
                $finalQuery->where('product_purchases.branchId', $branchId);
            }

            $finalQuery->orderBy('product_purchases.supplier_id')
                ->orderBy('product_purchases.product_name_id')
                ->orderBy('product_purchases.id', 'DESC');

            $purchase_data = $finalQuery->get();

            $purchase_data_array = '';
            if ($purchase_data->isNotEmpty()) {
                $last_id = '';

                foreach ($purchase_data as $index => $purchase) {
                    if ($pmonth == '') {
                        $pmonth = $tp_month->Month;
                    }

                    if ($pmonth == $tp_month->Month) {
                        $tpm++;
                        $tpmk = $tpm;
                    } else {
                        $tpmk = 1;
                        $tpm = 0;
                        $pmonth = '';
                    }

                    $sup_id = $purchase->supplier_id . '_' . $purchase->product_name_id;
                    $count_tm_cost = $query_count_data_array[$sup_id] ?? '0___0';
                    [$count, $tmc_cost] = explode('___', $count_tm_cost);

                    $purchase_data_array .= '<tr>
                        <td><div style="width: 65px;">' . e($purchase->purchase_date) . '</div></td>
                        <td>' . e($purchase->chalan_no) . '</td>
                        <td>' . e($purchase->sname) . '</td>
                        <td>' . e($purchase->pname) . '</td>
                        <td>' . round($purchase->product_qty, 3) . '</td>
                        <td>' . e($purchase->unit_type) . '</td>
                        <td>' . round($purchase->rate_per_unit, 3) . '</td>
                        <td>' . round($purchase->material_cost, 3) . '</td>';

                    if ($last_id != $sup_id) {
                        $purchase_data_array .= '<td rowspan="' . e($count) . '">' . round($tmc_cost, 3) . '</td>';
                    }

                    if ($tpmk == 1) {
                        $rowspan = $tp_month->purchase_count > 1 ? 'rowspan="' . $tp_month->purchase_count . '"' : '';
                        $purchase_data_array .= '<td ' . $rowspan . '>' . round($tp_month->total_mcost, 3) . '</td>';
                    }

                    $purchase_data_array .= '</tr>';
                    $last_id = $sup_id;
                }
            }

            $total_purchase_month_array[] = '<h3>' . date("F Y", mktime(0, 0, 0, $tp_month->Month, 1, $tp_month->Year)) . '</h3>
                <div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                        <thead>
                            <tr>
                                <th><div style="width: 65px;">Date</div></th>
                                <th>C.No</th>
                                <th>S.Name</th>
                                <th>P.Name</th>
                                <th>Qty</th>
                                <th>U.Type</th>
                                <th>RPU</th>
                                <th>M.Cost</th>
                                <th>T.M.Cost</th>
                                <th>G.T.M.Cost</th>
                            </tr>
                        </thead>
                        <tbody>' . $purchase_data_array . '</tbody>
                    </table>
                </div>';

            $pp++;
        }
    }

    /********************** SECOND SECTION: Aggregated Purchase List **********************/
    // 🔹 PURCHASE LIST WITH BRANCH NAME
    $purchaseListQuery = DB::table('product_purchases as pp')
        ->join('product_names as p', 'p.id', '=', 'pp.product_name_id')
        ->join('branches', 'branches.id', '=', 'pp.branchId')
        ->selectRaw('
            pp.product_name_id,
            p.name,
            branches.name as branch_name, 
            MONTH(pp.received_date) as month,
            YEAR(pp.received_date) as year,
            pp.unit_type,
            SUM(pp.product_qty) as total_qty,
            SUM(pp.total_material_cost) as total_mat_cost
        ')
        ->groupBy(DB::raw('
            MONTH(pp.received_date),
            YEAR(pp.received_date),
            p.name,
            pp.product_name_id,
            pp.unit_type,
            branches.name 
        '))
        ->orderBy('pp.received_date', 'DESC');

    // ব্রাঞ্চ ফিল্টারিং: সুপার অ্যাডমিন অথবা user_id=17 না হলে ব্রাঞ্চ ফিল্টার
    if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
        $purchaseListQuery->where('pp.branchId', $branchId);
    }
    $purchase_list = $purchaseListQuery->get();

    // 🔹 MONTHS LIST (no branch needed here)
    $monthsListQuery = DB::table('product_purchases as pp')
        ->selectRaw('MONTH(pp.received_date) as month, YEAR(pp.received_date) as year, COUNT(pp.received_date) as row_total')
        ->groupBy(DB::raw('MONTH(pp.received_date), YEAR(pp.received_date)'))
        ->orderBy('year', 'DESC')
        ->orderBy('month', 'DESC');

    // ব্রাঞ্চ ফিল্টারিং: সুপার অ্যাডমিন অথবা user_id=17 না হলে ব্রাঞ্চ ফিল্টার
    if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
        $monthsListQuery->where('pp.branchId', $branchId);
    }
    $months_list = $monthsListQuery->get();

    // 🔹 BUILD HTML WITH BRANCH NAME
    $html_purchase_list = [];
    $i = 0;
    foreach ($months_list as $pmlist) {
        $html_purchase_list[$i] = '<h3>' . date("F Y", mktime(0, 0, 0, $pmlist->month, 1, $pmlist->year)) . '</h3>
            <div>
                <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                    <thead>
                        <tr>
                            <th width="5%">SN</th>
                            <th width="20%">Material Name</th>
                            <th width="20%">Branch</th> 
                            <th width="20%">Total qty</th>
                            <th width="20%">Total Mat Cost</th>
                        </tr>
                    </thead>
                    <tbody>';

        $k = 0;
        foreach ($purchase_list as $list) {
            if ($pmlist->month == $list->month && $pmlist->year == $list->year) {
                $k++;
                $html_purchase_list[$i] .= '<tr>
                    <td>' . $k . '</td>
                    <td>' . e($list->name) . '</td>
                    <td>' . e($list->branch_name) . '</td> 
                    <td>' . round($list->total_qty, 2) . ' ' . e($list->unit_type) . '</td>
                    <td>' . round($list->total_mat_cost, 2) . '</td>
                </tr>';
            }
        }

        $html_purchase_list[$i] .= '</tbody></table></div>';
        $i++;
        if ($i >= 2) break; // Only show last 2 months
    }

    // 🔹 SALES LIST WITH BRANCH NAME
    $salesListQuery = DB::table('product_sales as ps')
        ->join('mix_designs', 'mix_designs.id', '=', 'ps.mix_design_id')
        ->join('branches', 'branches.id', '=', 'ps.branchId') 
        ->selectRaw('mix_designs.rate, mix_designs.psi, ps.sell_date, SUM(ps.cuM) as total_cum, branches.name as branch_name')
        ->groupBy('ps.sell_date', 'mix_designs.psi', 'mix_designs.rate', 'branches.name') 
        ->orderBy('ps.sell_date', 'DESC');

    // ব্রাঞ্চ ফিল্টারিং: সুপার অ্যাডমিন অথবা user_id=17 না হলে ব্রাঞ্চ ফিল্টার
    if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
        $salesListQuery->where('ps.branchId', $branchId);
    }
    $sales_list = $salesListQuery->get();

    // 🔹 SALES MONTHS SUMMARY (no branch name needed here)
    $salesMonthsQuery = DB::table('product_sales as ps')
        ->selectRaw('MONTH(ps.sell_date) as month, YEAR(ps.sell_date) as year, COUNT(ps.sell_date) as row_total, SUM(ps.cuM) as total_cum')
        ->groupBy(DB::raw('MONTH(ps.sell_date), YEAR(ps.sell_date)'))
        ->orderBy('year', 'DESC')
        ->orderBy('month', 'DESC');

    // ব্রাঞ্চ ফিল্টারিং: সুপার অ্যাডমিন অথবা user_id=17 না হলে ব্রাঞ্চ ফিল্টার
    if (!$isSuperAdmin && !$canViewAllBranches && $branchId) {
        $salesMonthsQuery->where('ps.branchId', $branchId);
    }
    $sales_months_list = $salesMonthsQuery->get();

    // 🔹 BUILD HTML WITH BRANCH NAME
    $html_sales_list = [];
    $i = 0;
    foreach ($sales_months_list as $smlist) {
        $html_sales_list[$i] = '<h3>' .
            date("F Y", mktime(0, 0, 0, $smlist->month, 1, $smlist->year)) .
            '<span style="float:right;margin-right:25px;">Total: ' . round($smlist->total_cum, 2) . '</span>
            </h3>
            <div>
                <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                    <thead>
                        <tr>
                            <th width="15%">Date</th>
                            <th width="15%">Branch</th> 
                            <th width="15%">PSI</th>
                            <th width="15%">Total cuM</th>
                            <th width="20%">Total</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($sales_list as $list) {
            $sellMonth = (int) date('m', strtotime($list->sell_date));
            $sellYear = (int) date('Y', strtotime($list->sell_date));

            if ($smlist->month == $sellMonth && $smlist->year == $sellYear) {
                $totalValue = $list->total_cum * 35.15 * $list->rate;
                $html_sales_list[$i] .= '<tr>
                    <td>' . date('d-M-y', strtotime($list->sell_date)) . '</td>
                    <td>' . e($list->branch_name) . '</td> 
                    <td>' . e($list->psi) . '</td>
                    <td>' . round($list->total_cum, 2) . '</td>
                    <td>' . number_format($totalValue, 2) . '</td>
                </tr>';
            }
        }

        $html_sales_list[$i] .= '</tbody></table></div>';
        $i++;
        if ($i >= 2) break; // Only last 2 months
    }

    /********************** PRODUCT STOCK (NO FILTER) **********************/
    $product_stocks = ProductStock::all(); // No filtering as per requirement

    return view('admin.report.report_quick', compact(
        'product_stocks',
        'html_purchase_list',
        'html_sales_list'
    ));
}
    public function customerDiscount(Request $request)
    {
        $discounts = collect();
        $date_info = '';

        $query = DB::table('updated_bill')
            ->join('bills', 'updated_bill.bill_id', '=', 'bills.id')
            ->join('customers', 'bills.customer_id', '=', 'customers.id')
            ->select(
                'customers.name as customer_name',
                'bills.transaction_id',
                'updated_bill.bill_id',
                'bills.total_amount_before_discount',
                'updated_bill.returned_cft',
                'updated_bill.returned_cum',
                'updated_bill.discount_amount',
                'updated_bill.created_at as discount_date'
            );

        $name = $request->query('search_name');
        $from = $request->query('from_date');
        $to = $request->query('to_date');

        if ($from && $to && $to < $from) {
            return back()->with('error', 'The To Date must be after From Date')->withInput();
        }

        if ($from && $to) {
            $query->whereBetween('updated_bill.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $date_info = "Showing results from " . date('d-m-Y', strtotime($from)) . " to " . date('d-m-Y', strtotime($to));
        }

        if ($name) {
            $query->where('customers.name', 'like', "%$name%");
        }

        $discounts = $query->orderBy('updated_bill.created_at', 'desc')->get();

        return view('admin.report.report_customer_discount_list', compact('discounts', 'date_info', 'request'));
    }


}

