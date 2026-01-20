<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\BankInfo;
use App\Models\BankStatement;
use App\Models\CashStatement;
use App\Models\Branch;

class BankController extends Controller
{
    public function index()
    {
        $banks = BankInfo::orderBy('bank_name','ASC')->get();
        return view('admin.bank.view_bank_info',['banks' => $banks]);
    }

    public function create()
    {
        return view('admin.bank.add_bank_info');
    }

    public function store(Request $request)
    {
        $rules = [
            'account_name' => 'required',
            'account_no' => 'required|unique:bank_infos',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'account_type' => 'required'
        ];

        $this->validate($request, $rules);

        $banks = new BankInfo();
        $banks->account_name = $request->account_name;
        $banks->account_no = $request->account_no;
        $banks->account_type = $request->account_type;
        $banks->bank_name = $request->bank_name;
        $banks->branch_name = $request->branch_name;
        $banks->description = $request->description;
        $banks->status = 1;
        $banks->user_id = auth()->user()->id;

        $status  = $banks->save();

        if($status) {
            Session::flash('message', 'Bank Added Successfully');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function update(Request $request)
    {
        $rules = [
            'account_name' => 'required',
            'account_no' => 'required',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'account_type' => 'required'
        ];

        $this->validate($request, $rules);

        $id = $request->id;
        $banks = BankInfo::find($id);
        $banks->account_name = $request->get('account_name');
        $banks->account_no = $request->get('account_no');
        $banks->account_type = $request->get('account_type');
        $banks->bank_name = $request->get('bank_name');
        $banks->branch_name = $request->get('branch_name');
        $banks->description = $request->get('description');
        $banks->status = $request->get('status');

        $status  = $banks->save();

        if($status) {
            Session::flash('message', 'Bank Updated Successfully');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Updating Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $bank = BankInfo::find($id);

        if($bank->bankStatements()->exists()){
            Session::flash('message', 'Cann\'t delete! Transaction record found with this bank');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
        $bank->delete();

        Session::flash('message', 'Bank Deleted Successfully');
        Session::flash('m-class', 'alert-success');
        return redirect()->back();
    }

    public function bankBalance(Request $request)
    {
        $bank = BankInfo::find($request->bank_id);
        return response()->json(["balance"=> number_format($bank->balance(),2).' BDT']);
    }

    public function loadBankInfo(Request $request)
    {
        $bank_id = $request->bank_id;
        $bank_info = BankInfo::where('id',$bank_id)->first();
        //$bank_installment_names = BankInstallmentInfo::where('bank_id',$bank_id)->get();

        $response = array(
            'account_name' => $bank_info->account_name,
            'account_no' => $bank_info->account_no,
            'branch_name' => $bank_info->branch_name,
            'account_type' => $bank_info->account_type,
        );


        return response()->json($response);
    }

    public function bankInvestments(Request $request)
    {
        $user_data = Auth::user();
        $bank_balance = BankStatement::sum(DB::raw('credit - debit'));

        if($request->date_range){
            $date_range = date_range_to_arr($request->date_range);
        }elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $bank_investments  = BankStatement::where('transaction_id', 'like', '%ADB%');

        if(isset($date_range)) {
            $bank_investments = $bank_investments->whereBetween('posting_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($request->search_text) {
            $bank_investments  = $bank_investments->where(function($query) use ($request) {
                $query->where('transaction_id', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('credit', 'LIKE', $request->search_text.'%')
                    ->orWhereHas('bank_info',function ($q) use ($request){
                        $q->where('bank_name', 'LIKE', '%'.$request->search_text.'%');
                    });
            });
        }

        if($user_data->branchId != '')
        {
            $bank_investments = $bank_investments->where('branchId',$user_data->branchId);
        }

        $bank_investments = $bank_investments->orderBy('id','DESC')->get();
        $bank_infos = BankInfo::orderBy('bank_name')->get();

        return view('admin.bank.bank_investments',compact('bank_infos','bank_investments','bank_balance'));
    }

    public function saveBankInvestment(Request $request)
    {
        $rules = [
            'bank_id' => 'required|numeric',
            'credit' => 'required|numeric|min:1',
            'description' => 'required'
        ];

        $this->validate($request, $rules);
        $bank_info = BankInfo::find($request->bank_id);
        $user_data  = Auth::user();
        $cheque_date = date('Y-m-d',strtotime($request->cheque_date));
        $max_row = BankStatement::max('id');


        $bank_invest = new BankStatement();
        $transaction_no = $max_row+1;
        $bank_invest->transaction_id = 'ADB-'.$transaction_no;
        $bank_invest->posting_date = date('Y-m-d');
        $bank_invest->ref_date = $cheque_date;
        $bank_invest->cheque_no = $request->cheque_no;
        $bank_invest->description = $request->description;
        $bank_invest->table_name = 'bank_statements';
        $bank_invest->credit = $request->credit;
        $bank_invest->bank_info_id = $request->bank_id;
        $bank_invest->balance = $bank_info->balance() + $request->credit;
        $bank_invest->branchId = $user_data->branchId;
        $bank_invest->user_id = $user_data->id;
        $status = $bank_invest->save();

        if($status)
        {
            Session::flash('message', 'Investment Saved Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

//    public function viewWithdrawBankAmount(Request $request)
//    {
//        $user_data = Auth::user();
//        if($request->date_range){
//            $date_range = date_range_to_arr($request->date_range);
//        } elseif($request->date_range == '' && $request->search_text == ''){
//            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
//        }
//
//        $with_banks  = BankStatement::where('transaction_id', 'like', '%WBA%');
//
//        if(isset($date_range)) {
//            $with_banks = $with_banks->whereBetween('ref_date', $date_range);
//        }
//        if($request->search_text != ""){
//            $with_banks  = $with_banks->where(function ($query) use ($request) {
//                $query->where('debit', $request->input('search_name'))
//                    ->orwhere('cheque_no', 'LIKE', '%'.$request->search_text.'%')
//                    ->orwhere('description', 'LIKE', '%'.$request->search_text.'%')
//                    ->orwhere('transaction_id', 'LIKE', $request->search_text.'%')
//                    ->orWhereHas('branch', function ($q) use ($request) {
//                        $q->where('branchName', 'LIKE', '%'.$request->search_text.'%');
//                    })->orWhereHas('bank_info', function ($q) use ($request) {
//                        $q->where('bank_name', 'LIKE', '%'.$request->search_text.'%');
//                    });
//            });
//        }
//
//        if($user_data->branchId != '') {
//            $with_banks = $with_banks->where('branchId', '=', $user_data->branchId);
//        }
//
//        $with_banks = $with_banks->orderBy('id','DESC')->get();
//        $banks = BankInfo::orderBy('bank_name', 'ASC')->get();
//        $total_balance = BankStatement::sum(DB::raw('credit - debit'));
//        $branches  = Branch::orderBy('name', 'ASC')->get();
//
//        return view('admin.bank.withdraw_bank_amount',compact('banks','total_balance','with_banks','branches'));
//    }


    public function viewWithdrawBankAmount(Request $request)
    {
        $user_data = Auth::user();

        // Handle date range
        if ($request->filled('date_range')) {
            $date_range = date_range_to_arr($request->date_range);
        }
        else {
            $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        }

        // Initial Query
        $with_banks = BankStatement::where('transaction_id', 'LIKE', '%WBA%');

        // Apply Date Range Filter
        if (!empty($date_range)) {
            $with_banks = $with_banks->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        // Apply Search Filter Only If Input is Provided
        if (!empty($request->search_text)) {
            $with_banks = $with_banks->where(function ($query) use ($request) {
                $query->where('debit', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('cheque_no', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('transaction_id', 'LIKE', $request->search_text.'%');

                // Branch Name Search
                $query->orWhereHas('branch', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%'.$request->search_text.'%');
                });

                // Bank Name Search
                $query->orWhereHas('bank_info', function ($q) use ($request) {
                    $q->where('bank_name', 'LIKE', '%'.$request->search_text.'%');
                });
            });
        }

        // Filter By User's Branch If Needed
        if (!empty($user_data->branchId)) {
            $with_banks = $with_banks->where('branchId', $user_data->branchId);
        }

        // Execute the Query
        $with_banks = $with_banks->orderBy('id', 'DESC')->get();

        // Fetch Other Required Data
        $banks = BankInfo::orderBy('bank_name', 'ASC')->get();
        $total_balance = BankStatement::sum(DB::raw('credit - debit'));
        $branches = Branch::orderBy('name', 'ASC')->get();

        return view('admin.bank.withdraw_bank_amount', compact('banks', 'total_balance', 'with_banks', 'branches'));
    }


    public function saveWithdrawBankAmount(Request $request)
    {
        $rules = [
            'bank_id' => 'required|numeric',
            'debit' => 'required|numeric',
            'description' => 'required',
            'cheque_no' => 'required',
            'cheque_date' => 'required',
            'branchId' => 'nullable|numeric'
        ];

        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            $user_data  = Auth::user();
            $cheque_date = date('Y-m-d', strtotime($request->cheque_date));

            $bank_info = BankInfo::find($request->bank_id);
            $bank_bal = $bank_info->balance();
            if ($request->debit > $bank_bal) {
                throw new \Exception('Insufficient Balance in bank');
            }

            $max_row = BankStatement::max('id');
            $transaction_no = $max_row + 1;

            if ($request->branchId == '') {
                $transaction_id = 'WBA-' . $transaction_no;
                $description = 'Withdraw bank amount - ' . $request->description;
            } else {
                $transaction_id = 'WBAFB-' . $transaction_no;
                $description = 'Withdraw bank amount for branch - ' . $request->description;
            }

            $b_statement = new BankStatement();
            $b_statement->transaction_id = $transaction_id;
            $b_statement->description = $description;
            $b_statement->posting_date = date('Y-m-d');
            $b_statement->ref_date = $cheque_date;
            $b_statement->cheque_no = $request->cheque_no;
            $b_statement->table_name = 'bank_statements';
            $b_statement->debit = $request->debit;
            $b_statement->bank_info_id = $request->bank_id;
            $b_statement->branchId = $request->branchId;
            $b_statement->user_id = $user_data->id;
            $b_statement->balance = $bank_bal - $request->debit;
            $status = $b_statement->save();

            //save data in cash_statements
            $c_statement = new CashStatement();
            $c_statement->transaction_id = $transaction_id;
            $c_statement->posting_date = date('Y-m-d');
            $c_statement->description = $description;
            $c_statement->table_name = 'bank_statements';
            $c_statement->ref_date = $cheque_date;
            $c_statement->receipt_no = $request->cheque_no;
            $c_statement->credit = $request->debit;
            $c_statement->balance = cashBalance($request->branchId) + $request->debit;
            $c_statement->branchId = $request->branchId;
            $c_statement->user_id = $user_data->id;
            $c_statement->save();


            if ($status) {
                DB::commit();
                Session::flash('message', 'Balance Deducted Successfully');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollBack();
                Session::flash('message', 'Saving Data failed!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back();
            }
        } catch(\Exception $ex){
            DB::rollBack();
            Session::flash('message',$ex->getMessage());
            Session::flash('m-class','alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function viewBankStatement(Request $request)
    {
        $banks = BankInfo::orderBy('bank_name')->get();
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        }elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }


        $bank_statements  = BankStatement::query();
        if(isset($date_range)){
            $bank_statements = $bank_statements->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }


        /*SEARCH DATA WITH BRANCH AND DATE RANGE*/
        if ($request->bank_id != ''){
            $bank_statements = $bank_statements->where('bank_id', $request->bank_id);
        }

        $bank_statements = $bank_statements->orderBy('ref_date','desc')->get();

        return view('admin.bank.view_bank_statement',compact('bank_statements','banks'));
    }

    public function deleteBankAmount($tran_id)
    {
        $status = BankStatement::where('transaction_id',$tran_id)->delete();
        $status2 = CashStatement::where('transaction_id',$tran_id)->delete();

        if($status) {
            Session::flash('message', 'Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Deleting failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }
}


