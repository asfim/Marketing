<?php

namespace App\Http\Controllers;

use App\Models\BankStatement;
use App\Models\Branch;
use App\Models\CashStatement;
use App\Models\BlanceTransfer;
use App\Models\CustomerPayment;
use App\Models\CustomerStatement;
use App\Models\Expense;
use App\Models\ProductPurchase;
use App\Models\ProductSale;
use App\Models\ProductStock;
use App\Models\Income;
use App\Models\Asset;
use App\Models\SupplierPayment;
use App\Models\SupplierStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function view(Request $request)
    {
        $branches = Branch::orderBy('name')->get();
        $user = Auth::user();
        $branchId = $request->branchId??$user->branchId;

        if($request->date_range){
            $date_range = date_range_to_arr($request->date_range);
        }

        $cash_statement = new CashStatement();
        $bank_statement = new BankStatement();
        $expense_data = new Expense();
        $income_data = new Income();
        $supplier_payment = new SupplierPayment();
        $customer_payment = new CustomerPayment();
        $product_purchase = new ProductPurchase();
        $supplier_statement = new SupplierStatement();
        $customer_statement = new CustomerStatement();
        $bank_investment = BankStatement::where('transaction_id','like','%ADB%');
        $cash_investment = CashStatement::where('transaction_id','like','%AC%');
        $uncheck_bills  = ProductSale::where('status',1);

        /* SEARCH DATA WITH BRANCH AND DATE RANGE */
        if($branchId == 'head_office'){
            $cash_statement = $cash_statement->where('branchId', null);
            $expense_data = $expense_data->where('branchId', null);
            $income_data = $income_data->where('branchId', null);
            $supplier_payment = $supplier_payment->where('branchId', null);
            $product_purchase = $product_purchase->where('branchId', null);
            $supplier_statement = $supplier_statement->where('branchId', null);
            $customer_statement = $customer_statement->where('branchId', null);
            $uncheck_bills = $uncheck_bills->where('branchId', null);
        } elseif ($branchId != ''){
            $cash_statement = $cash_statement->where('branchId', $branchId);
            $supplier_payment = $supplier_payment->where('branchId', $branchId);
            $product_purchase = $product_purchase->where('branchId', $branchId);
            $supplier_statement = $supplier_statement->where('branchId', $branchId);
            $customer_statement = $customer_statement->where('branchId', $branchId);
            $uncheck_bills = $uncheck_bills->where('branchId', $branchId);
        }

        if(isset($date_range)){
            $cash_statement = $cash_statement->whereBetween('ref_date', $date_range);
            $bank_statement = $bank_statement->whereBetween('ref_date', $date_range);
            $expense_data = $expense_data->whereBetween('date', $date_range);
            $income_data = $income_data->whereBetween('date', $date_range);
            $supplier_payment = $supplier_payment->whereBetween('ref_date', $date_range);
            $customer_payment = $customer_payment->whereBetween('ref_date', $date_range);
            $product_purchase = $product_purchase->whereBetween('received_date', $date_range);
            $supplier_statement = $supplier_statement->whereBetween('posting_date', $date_range);
            $customer_statement = $customer_statement->whereBetween('posting_date', $date_range);
            $cash_investment = $cash_investment->whereBetween('ref_date', $date_range);
            $bank_investment = $bank_investment->whereBetween('ref_date', $date_range);
            $uncheck_bills = $uncheck_bills->whereBetween('sell_date', $date_range);
        }

        /* Cash Amount */
        $inflow['cash'] = $cash_statement->sum('credit');
        $outflow['cash'] = $cash_statement->sum('debit');
        /* Bank Amount */
        $inflow['bank'] = $bank_statement->sum('credit');
        $outflow['bank'] = $bank_statement->sum('debit');

        /* EXPENSE DATA */
        $expense['total'] = 0;
        if(Auth::user()->branchId == null){
            $expense['total'] = $expense_data->sum('amount');
        }else{
            $expense['total'] = $expense_data->where('branchId', Auth::user()->branchId)->sum('amount');
        }
        $expense['general'] = $expense_data->whereHas('expense_type',function($query){
            $query->where('category','General Expense');
        })->sum('amount');
        $expense['production'] = $expense_data->whereHas('expense_type',function($query){
            $query->where('category','Production Expense');
        })->sum('amount');

        /* INCOME DATA */
        $income['total'] = $income_data->sum('amount');
        $income['general'] = $income_data->whereHas('income_type',function($query){
            $query->where('category','General Income');
        })->sum('amount');
        $income['waste'] = $income_data->whereHas('income_type',function($query){
            $query->where('category','Waste Income');
        })->sum('amount');

        /* SUPPLIER/CUSTOMER PAYMENT */
        $supplier['payment'] = $supplier_payment->sum('paid_amount');
        $supplier['adjustment'] = $supplier_payment->sum('adjustment_amount');
        $supplier['due'] = $supplier_statement->sum(DB::raw('credit - debit'));
        $customer['payment'] = $customer_payment->sum('paid_amount');
        $customer['adjustment'] = $customer_payment->sum('adjustment_amount');
        $customer['due'] = $customer_statement->sum(DB::raw('credit - debit'));

        $p_purchase['total'] = $product_purchase->sum('material_cost');
        $p_purchase['truck_rent'] = $product_purchase->sum('truck_rent');
        $p_purchase['unload_bill'] = $product_purchase->sum('unload_bill');

        $investment['cash'] = $cash_investment->sum('credit');
        $investment['bank'] = $bank_investment->sum('credit');
        $uncheck_bills = $uncheck_bills->get();
//        $total_billable=0;
//        foreach($uncheck_bills as $bill){
//            $total_billable += ($bill->cuM*35.315) * $bill->mix_design->rate;
//        }



        $total_billable = 0;

        foreach ($uncheck_bills as $bill) {
            $qty_cft = $bill->cuM * 35.315;

            $rate = $bill->rate > 0 ? $bill->rate : $bill->mix_design->rate;

            $total_billable += $qty_cft * $rate;
        }


        //asset purchase as expense
        $asset_expense = 0;
        $purchase_amounts = Asset::pluck('purchase_amount');

        if($purchase_amounts != "")
        {
            foreach ($purchase_amounts as $amount)
            {
                $asset_expense += $amount;
            }

        }



        return view('admin.dashboard', compact('branches', 'investment',
            'inflow','outflow','expense','income','supplier','customer','p_purchase','total_billable'));
            
    }
}
