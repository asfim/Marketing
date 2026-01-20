<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\CashStatement;
use App\Models\BlanceTransfer;
use App\Models\BranchBalance;

class CashController extends Controller
{
    public function index(Request $request)
    {
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        }elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $ad_cash  = CashStatement::where('transaction_id', 'like', '%AC%');

        if(isset($date_range)) {
            $ad_cash = $ad_cash->whereBetween('posting_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }


        if($request->search_text != ""){
            $ad_cash  = $ad_cash->where(function($query) use ($request) {
                $query->where('transaction_id', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('credit', 'LIKE', $request->search_text.'%');
            });
        }

        $ad_cash = $ad_cash->orderBy('id','DESC')->get();

        $cash_balance = CashStatement::sum(DB::raw('credit - debit'));

        return view('admin.cash.cash_amount',compact('ad_cash','cash_balance'));
    }

    public function saveCash(Request $request)
    {
        $rules = [
            'credit' => 'required|numeric|min:1',
            'posting_date' => 'required'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        $posting_date = date('Y-m-d',strtotime($request->posting_date));

        $cash = new CashStatement();
        $transaction_no = CashStatement::max('id')+1;
        $cash->transaction_id = 'AC-'.$transaction_no;
        $cash->posting_date = $posting_date;
        $cash->ref_date = $posting_date;
        $cash->receipt_no = $request->receipt_no;
        $cash->description = 'Investment Cash - '.$request->description;
        $cash->table_name = 'cash_statements';
        $cash->credit = $request->credit;
        $cash->branchId   = $user_data->branchId;
        $cash->user_id   = $user_data->id;
        //CHECK CASH BALANCE
        $cash_bal = cashBalance($request->branchId);
        $cash->balance = $cash_bal + $request->credit;
        $status = $cash->save();

        if($status) {
            Session::flash('message', 'Cash Added Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

    }

    public function updateCash(Request $request)
    {
        $rules = [
            'credit' => 'required',
        ];

        $this->validate($request, $rules);

        $e_cash = CashStatement::find($request->id);
        $e_cash->description=$request->description;
        $e_cash->credit=$request->credit;
        $status  = $e_cash->save();


        if($status) {
            Session::flash('message', 'Cash Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function deleteCash($id)
    {
        $cash = CashStatement::find($id);
        $status = $cash->delete();

        if($status) {
            Session::flash('message', 'Cash Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

//    public function viewWithdrawCash(Request $request)
//    {
//        $user_data = Auth::user();
//       // dd($request);
//        if($request->date_range){
//            $date_range = date_range_to_arr($request->date_range);
//          //  dd($date_range);
//        } elseif($request->date_range == '' && $request->search_text == ''){
//            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
//           // dd($date_range);
//        }
//
//        $with_cash  = CashStatement::where('transaction_id', 'like', '%WC%');
//
//        if(isset($date_range)) {
//            $with_cash = $with_cash->whereBetween('posting_date', $date_range);
//           // dd($with_cash);
//        }
//        if($request->search_text != ""){
//
//            $with_cash  = $with_cash->where(function ($query) use ($request) {
//                $query->where('debit', $request->search_text)
//                    ->orWhere('transaction_id','LIKE',$request->search_text.'%')
//                    ->orWhere('description','LIKE', '%'.$request->search_text.'%')
//                    ->orWhereHas('branch', function ($q) use ($request) {
//                        $q->where('name', 'LIKE', '%'.$request->search_text.'%');
//                    });
//            });
//        }
//
//        if($user_data->branchId != '') {
//            $with_cash = $with_cash->where('branchId', $user_data->branchId);
//        }
//
//        $with_cash = $with_cash->orderBy('id','DESC')->get();
//
//        $branches  = Branch::where('id','!=',$user_data->branchId)->orderBy('name', 'ASC')->get();
//
//        return view('admin.cash.withdraw_cash_amount',compact('with_cash', 'branches'));
//    }



    public function viewWithdrawCash(Request $request)
    {
        $user_data = Auth::user();

        if ($request->filled('date_range')) {
            $date_range = date_range_to_arr($request->date_range);
        } elseif (empty($request->date_range) && empty($request->search_text)) {
            $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        }

        // Initial Query
        $with_cash = CashStatement::where('transaction_id', 'LIKE', '%WC%');

        // Apply Date Range Filter
        if (!empty($date_range)) {
            $with_cash = $with_cash->whereBetween('posting_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        // Apply Search Filter Only If Input is Provided
        if (!empty($request->search_text)) {
            $with_cash = $with_cash->where(function ($query) use ($request) {
                $query->where('debit', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('credit', 'LIKE', '%'.$request->search_text.'%') // Added for credit search
                    ->orWhere('transaction_id', 'LIKE', $request->search_text.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhereHas('branch', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%'.$request->search_text.'%');
                    });
            });
        }

        // Filter By User Branch If Needed
        if (!empty($user_data->branchId)) {
            $with_cash = $with_cash->where('branchId', $user_data->branchId);
        }

        // Execute the Query
        $with_cash = $with_cash->orderBy('posting_date', 'DESC')->get();

        $branches = Branch::where('id', '!=', $user_data->branchId)->orderBy('name', 'ASC')->get();

        return view('admin.cash.withdraw_cash_amount', compact('with_cash', 'branches'));
    }


    public function saveWithdrawCash(Request $request)
    {
        $user_data = Auth::user();
        $rules = [
            'amount' => 'required|numeric',
            'posting_date' => 'required'
        ];

        if ($user_data->branchId == '') {$rules['branchId']='required|numeric';}
        else{$rules['branchId']='nullable|numeric|not_in:'.$user_data->branchId;}
        $this->validate($request, $rules);

        $posting_date = date('Y-m-d',strtotime($request->posting_date));
        DB::beginTransaction();
        try {
            $transaction_no = CashStatement::max('id') + 1;

            //checking branch for transaction
            if ($request->branchId == '') {
                $transaction_id = 'WC-' . $transaction_no;
                $description = 'Withdraw Cash - ' . $request->description;
            } else {
                $transaction_id = 'WCFB-' . $transaction_no;
                $description = 'Withdraw Cash for branch - ' . $request->description;
            }

            //checking cash balance
            if(cashBalance($user_data->branchId) < $request->amount){
                throw new \Exception('Insufficient balance in cash');
            }
            $current_bal_from = cashBalance($user_data->branchId) - $request->amount;

            $cash = new CashStatement();
            $cash->transaction_id = $transaction_id;
            $cash->posting_date = $posting_date;
            $cash->ref_date = $posting_date;
            $cash->receipt_no = $request->receipt_no;
            $cash->description = $description;
            $cash->table_name = 'blance_transfers';
            $cash->debit = $request->amount;
            $cash->balance = $current_bal_from;
            $cash->branchId = $user_data->branchId;
            $cash->user_id = $user_data->id;
            $cash->save();

            //TODO::Use balance Transfer table
//            //save data in BalanceTransfer
//            $cash = new BlanceTransfer();
//            $transaction_no = BlanceTransfer::max('id') + 1;
//            $cash->transaction_id = 'WCFB-' . $transaction_no;
//            $cash->description = 'Withdraw Cash for branch to branch - ' . $request->description;
//            $cash->posting_date = $posting_date;
//            $cash->user_id = $user_data->id;
//            $cash->receipt_no = $request->receipt_no;
//            $cash->table_name = 'blance_transfers';
//            $cash->debit = $request->amount;
//            $cash->credit = $request->amount;
//            $cash->branchId = $request->branchId;
//            $status = $cash->save();

            //CREDITED AMOUNT TO
            $current_bal_to = cashBalance($request->branchId) + $request->amount;

            //save data in CashStatement
            $cash = new CashStatement();
            $cash->transaction_id = $transaction_id;
            $cash->posting_date = $posting_date;
            $cash->ref_date = $posting_date;
            $cash->receipt_no = $request->receipt_no;
            $cash->description = 'Withdraw Cash - ' . $request->description;
            $cash->table_name = 'cash_statements';
            $cash->credit = $request->amount;
            $cash->balance = $current_bal_to;
            $cash->branchId = $request->branchId;
            $cash->user_id = $user_data->id;
            $cash->save();

            DB::commit();
            Session::flash('message', 'Withdraw Cash Added Successfully');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch(\Exception $ex){
            DB::rollBack();
            Session::flash('message',$ex->getMessage());
            Session::flash('m-class','alert-danger');
            return redirect()->back()->withInput();
        }

    }

    public function deleteCashAmount($tran_id)
    {
        $status = CashStatement::where('transaction_id',$tran_id)->delete();

        if($status) {
            Session::flash('message', 'Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Deleted Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function viewCashStatement(Request $request)
    {
        $user = Auth::user();
        $branches = Branch::orderBy('name')->get();
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }
        $branchId = $request->branchId??$user->branchId;


        $cash_statements  = CashStatement::query();

        /*SEARCH DATA WITH BRANCH AND DATE RANGE*/
        if(isset($date_range)){
            $cash_statements = $cash_statements->whereBetween('ref_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($branchId == 'head_office'){
            $cash_statements = $cash_statements->where('branchId', null);
        } elseif ($branchId != ''){
            $cash_statements = $cash_statements->where('branchId', $branchId);
        }

        $cash_statements = $cash_statements->orderBy('ref_date','desc')->get();
        return view('admin.cash.view_cash_statement',compact('cash_statements','branches'));
    }

    public function viewBlanceTransfer(Request $request)
    {
        $user_data      = Auth::user();
        $cashObj        = new BlanceTransfer();
        if($request->input('search_name') != "" || ($request->input('from_date') != "" && $request->input('to_date') != "")) {

            $from_date  = date("Y-m-d", strtotime($request->input('from_date')));
            $to_date    = date("Y-m-d", strtotime($request->input('to_date')));
            if($request->input('search_name') != "" && $request->input('from_date') != "" && $request->input('to_date') != "") {
                $with_cash = $cashObj->where('transaction_id', 'like', '%WCFB%')
                    ->where('user_id', '=', $user_data->id)
                    ->whereBetween('posting_date', [$from_date, $to_date])
                    ->where(function ($query) use ($request) {
                        $query->where('debit', '=', $request->input('search_name'))
                            ->orWhere('transaction_id','LIKE',$request->input('search_name').'%')
                            ->orWhere('description','LIKE', '%'.$request->input('search_name').'%')
                            ->orWhereHas('branches', function ($q) use ($request) {
                                $q->where('branchName', 'LIKE', '%'.$request->input('search_name').'%');
                            });
                    })->orderBy('id','DESC')->get();

            }elseif($request->input('search_name') == "" && $request->input('from_date') != "" && $request->input('to_date') != "") {

                $with_cash = $cashObj->where('transaction_id', 'like', '%WCFB%')
                    ->where('user_id', '=', $user_data->id)
                    ->whereBetween('posting_date', [$from_date, $to_date])
                    ->orderBy('id','DESC')
                    ->get();

            }elseif($request->input('search_name') != "" && $request->input('from_date') == "" && $request->input('to_date') == "") {

                $with_cash = $cashObj->where('transaction_id', 'like', '%WC%')
                    ->where('user_id', '=', $user_data->id)
                    ->where(function ($query) use ($request) {
                        $query->where('debit', '=', $request->input('search_name'))
                            ->orwhere('posting_date', '=', $request->input('search_name'))
                            ->orWhere('transaction_id','LIKE',$request->input('search_name').'%')
                            ->orWhere('description','LIKE', '%'.$request->input('search_name').'%')
                            ->orWhereHas('branches', function ($q) use ($request) {
                                $q->where('branchName', 'LIKE', '%'.$request->input('search_name').'%');
                            });
                    })->orderBy('id','DESC')->get();

            }

        }else {

            $with_cash = $cashObj->where('transaction_id', 'like', '%WCFB%')
                ->where('user_id', '=', $user_data->id)
                ->orderBy('id','DESC')
                ->get();
            //dd($with_cash);
        }


        $all_statements = BlanceTransfer::all();
        $cash_balance = 0;
        foreach($all_statements as $statement)
        {
            $cash_balance += $statement->credit - $statement->debit;
        }
        return view('admin.cash.blance_transfer',compact('with_cash','cash_balance'));
    }

}


