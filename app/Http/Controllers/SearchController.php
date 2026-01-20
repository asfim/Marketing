<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;
use App\Models\BankInfo;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\IncomeType;
use App\Models\Income;
use App\Models\Customer;
use App\Models\BankInstallmentInfo;
use App\Models\Asset;
use App\Models\OwnerProfile;
use App\Models\Branch;
use DB;

class SearchController extends Controller
{
    public function autocomplete(Request $request)
    {
        $text = $request->term;
        $table_name = $request->table_name;
        $user_data  = Auth::user();
        $data = array();
        //for supplier name search in supplier
        if($table_name == 'suppliers')
        {
            $data = Supplier::where('name','LIKE',$text.'%')
                ->take(10)
                ->pluck('name');
            return response()->json($data);
        }

        //for supplier name search in supplier
        if($table_name == 'customers')
        {
            $data = Customer::where('name','LIKE',$text.'%')
                ->take(10)
                ->pluck('name');
            return response()->json($data);
        }
        //for customer name search 
        if($table_name == 'assets')
        {
            $data = Asset::where('name','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('name');
            return response()->json($data);
        }

        //for bank installment name search 
        if($table_name == 'bank_ins')
        {
            $data = BankInstallmentInfo::where('installment_name','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('installment_name');

            return response()->json($data);
        }

        //for General Expense name search 
        if($table_name == 'expenses')
        {
            $data = Expense::where('expense_name','LIKE','%'.$text.'%')
                ->where('transaction_id','LIKE','GE%')
                ->where('branchId',$user_data->branchId)
                ->take(10)
                ->pluck('expense_name');

            return response()->json($data);
        }

        if($table_name == 'expense_types')
        {
            $data = ExpenseType::where('type_name','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('type_name');

            return response()->json($data);
        }

        //for owner name search 
        if($table_name == 'land_owner_profiles')
        {
            $data = OwnerProfile::where('name','LIKE','%'.$text.'%')
                ->where('type','Land Owner')
                ->take(10)
                ->pluck('name');
            return response()->json($data);
        }

        if($table_name == 'house_owner_profiles')
        {
            $data = OwnerProfile::where('name','LIKE','%'.$text.'%')
                ->where('type','House Owner')
                ->take(10)
                ->pluck('name');

            return response()->json($data);
        }

        //income search
        if($table_name == 'gen_incomes')
        {
            $data = Income::where('income_name','LIKE','%'.$text.'%')
                ->where('transaction_id','LIKE','GI%')
                ->take(10)
                ->pluck('income_name');

            return response()->json($data);
        }

        if($table_name == 'waste_incomes')
        {
            $data = Income::where('income_name','LIKE','%'.$text.'%')
                ->where('transaction_id','LIKE','WI%')
                ->take(10)
                ->pluck('income_name');

            return response()->json($data);
        }

        if($table_name == 'income_type')
        {
            $data = IncomeType::where('type_name','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('type_name');

            return response()->json($data);
        }

        if($table_name == 'branches')
        {
            $data = Branch::where('branchName','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('branchName');
            return response()->json($data);
        }

        if($table_name == 'bank_infos')
        {
            $data = BankInfo::where('bank_name','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('bank_name');

            return response()->json($data);

        }

        if($table_name == 'bank_acc')
        {
            $data = BankInfo::where('account_no','LIKE','%'.$text.'%')
                ->take(10)
                ->pluck('account_no');

            return response()->json($data);

        }
        //echo json_encode($data);
//        return response()->json($data);


    }

//    public function searchGlobal(Request $request)
//    {
//       // dd("df");
//        if($request->g_search_text != "") {
//            $customers  = Customer::where('name', 'LIKE', '%'.$request->g_search_text.'%')
//                ->orWhere('email', 'LIKE', '%'.$request->g_search_text.'%')
//                ->orWhere('phone', 'LIKE', $request->g_search_text.'%')
//                ->orWhere('extra_phone_no', 'LIKE', '%'.$request->g_search_text.'%')
//                ->orderBy('name','ASC')
//                ->get();
//
//            $suppliers  = Supplier::where('name', 'LIKE', '%'.$request->g_search_text.'%')
//                ->orWhere('email', 'LIKE', '%'.$request->g_search_text.'%')
//                ->orWhere('phone', 'LIKE', $request->g_search_text.'%')
//                ->orWhere('extra_phone_no', 'LIKE', '%'.$request->g_search_text.'%')
//                ->orderBy('name','ASC')
//                ->get();
//        }
//
//        return view('admin.pages.search',compact('customers','suppliers'));
//    }


    public function searchGlobal(Request $request)
    {
        // Ensure the correct input field name is used
        if (!empty($request->search_text)) {
            $customers = Customer::where('name', 'LIKE', '%'.$request->search_text.'%')
                ->orWhere('email', 'LIKE', '%'.$request->search_text.'%')
                ->orWhere('phone', 'LIKE', $request->search_text.'%')
                ->orWhere('extra_phone_no', 'LIKE', '%'.$request->search_text.'%')
                ->orderBy('name', 'ASC')
                ->get();

            $suppliers = Supplier::where('name', 'LIKE', '%'.$request->search_text.'%')
                ->orWhere('email', 'LIKE', '%'.$request->search_text.'%')
                ->orWhere('phone', 'LIKE', $request->search_text.'%')
                ->orWhere('extra_phone_no', 'LIKE', '%'.$request->search_text.'%')
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            // If no search text is provided, return an empty collection
            $customers = collect();
            $suppliers = collect();
        }

        return view('admin.pages.search', compact('customers', 'suppliers'));
    }

}
