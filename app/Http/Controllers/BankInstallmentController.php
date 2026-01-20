<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\BankInfo;
use App\Models\BankStatement;
use App\Models\BankInstallmentInfo;
use App\Models\BankInstallmentLog;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\CashStatement;
use App\Models\Branch;

class BankInstallmentController extends Controller
{
    public function index(Request $request)
    {
        $user_data = Auth::user();
        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        } elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $installments =  BankInstallmentInfo::query();


        if(isset($date_range)) {
            $installments = $installments->whereBetween('start_date', $date_range);
        }
        if($request->search_text != ""){
            $installments  = $installments->where(function($query) use ($request) {
                $query->where('installment_name', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('installment_number', $request->search_text)
                    ->orWhere('monthly_amount', $request->search_text)
                    ->orWhere('interest_rate', $request->search_text)
                    ->orWhere('total_loan', $request->search_text)
                    ->orWhereHas('bank_info', function($q) use ($request) {
                        $q->where('bank_name', 'LIKE', '%'.$request->search_text.'%');
                    });
            });
        }

        if(!$user_data->hasRole(['super-admin', 'admin'])) {
            $installments = $installments->where('branchId', '=', $user_data->branchId);
        }

        $installments = $installments->orderBy('id','DESC')->get();

        $bank_infos = BankInfo::where('account_type','Like','%Loan%')->get();
        return view('admin.bank.view_bank_loan_info',compact('installments','bank_infos'));
    }

    public function create()
    {
        $banks = BankInfo::where('account_type','=','Loan')->get();
        return view('admin.bank.add_bank_loan_info',['banks' => $banks]);
    }

    public function store(Request $request)
    {
        $rules = [
            'bank_id' => 'required|numeric',
            'installment_name' => 'required|unique:bank_installment_infos',
            'installment_number' => 'required|numeric',
            'monthly_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'total_loan' => 'required|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        $start_date = date('Y-m-d',strtotime($request->start_date));
        $end_date = date('Y-m-d',strtotime($request->end_date));

        //save files in server
        $file_names = array();
        if ($request->hasFile('file'))
        {
            $files = $request->file('file');
            foreach ($files as $file)
            {
                $ext = $file->extension();
                //$file_name = 'product-'.time().'.'.$ext;
                $original_file_name = $file->getClientOriginalName();
                $ex_file_name = explode('.',$original_file_name);
                $file_name = $ex_file_name[0].'-'.rand(1, 1500000).'.'.$ext;
                $destinationPath = public_path('img/files/bank_installment_info');
                $file->move($destinationPath, $file_name);
                $file_names[] = $file_name;
            }

        }
        $com_file_names = implode(',',$file_names);

        $banks = new BankInstallmentInfo();
        $banks->bank_id = $request->get('bank_id');
        $banks->installment_name = $request->installment_name;
        $banks->installment_number = $request->installment_number;
        $banks->monthly_amount = $request->monthly_amount;
        $banks->interest_rate = $request->interest_rate;
        $banks->total_loan = $request->total_loan;
        $banks->start_date = $start_date;
        $banks->end_date = $end_date;
        $banks->file = $com_file_names;
        $banks->description = $request->description;
        $banks->status = 1;
        $banks->user_id = $user_data->id;
        $status  = $banks->save();


        if ($status) {
            Session::flash('message', 'Installment Added Successfully');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

    }

    public function edit(Request $request, $id)
    {
        $installment = BankInstallmentInfo::findOrFail($id);
        $banks = BankInfo::where('account_type','=','Loan')->get();
        return view('admin.bank.edit_bank_loan_info',compact('installment','banks'));
    }

    public function update(Request $request)
    {
        $rules = [
            'bank_id' => 'required|numeric',
            'installment_name' => 'required',
            'installment_number' => 'required|numeric',
            'monthly_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'total_loan' => 'required|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        $start_date = date('Y-m-d',strtotime($request->start_date));
        $end_date = date('Y-m-d',strtotime($request->end_date));

        //save files in server
        $file_names = array();
        $com_file_names = "";
        if ($request->hasFile('file'))
        {
            $files = $request->file('file');
            foreach ($files as $file)
            {
                $ext = $file->extension();
                //$file_name = 'product-'.time().'.'.$ext;
                $original_file_name = $file->getClientOriginalName();
                $ex_file_name = explode('.',$original_file_name);
                $file_name = $ex_file_name[0].'-'.rand(1, 1500000).'.'.$ext;
                $destinationPath = public_path('img/files/bank_installment_info');
                $file->move($destinationPath, $file_name);
                $file_names[] = $file_name;
            }
            $com_file_names = implode(',',$file_names);
        }

        $id = $request->id;
        $banks = BankInstallmentInfo::find($id);
        $banks->bank_id = $request->bank_id;
        $banks->installment_name = $request->installment_name;
        $banks->installment_number = $request->installment_number;
        $banks->monthly_amount = $request->monthly_amount;
        $banks->interest_rate = $request->interest_rate;
        $banks->total_loan = $request->total_loan;
        $banks->start_date = $start_date;
        $banks->end_date = $end_date;
        if ($request->hasFile('file'))
        {
            $banks->file = $com_file_names;
        }

        $banks->description = $request->description;
        $banks->status = $request->status;
        $banks->user_id = $user_data->id;
        $status  = $banks->save();

        if ($status) {
            Session::flash('message', 'Updated Successfully');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function payInstallment($infoId)
    {
        $installment_info = BankInstallmentInfo::where('id',$infoId)->first();
        $bank_id = $installment_info->bank_id;
        $bank_info = BankInfo::where('id',$bank_id)->first();
        $all_banks = BankInfo::where('account_type', '<>', 'Loan')->get();
        $branches = Branch::all();
        return view('admin.bank.pay_installment',compact('installment_info','bank_info','all_banks','branches'));
    }

    public function saveInstallmentPayment(Request $request)
    {
        $rules = [
            'installment_info_id' => 'required|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'installment_paid' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'cheque_date' => 'required',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        $cheque_date = date('Y-m-d',strtotime($request->cheque_date));
        DB::beginTransaction();
        try {
            //save files in server
            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    //$file_name = 'product-'.time().'.'.$ext;
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;
                    $destinationPath = public_path('img/files/pay_installment_files');
                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }

            }
            $com_file_names = implode(',', $file_names);

            $ins_log_max_id = BankInstallmentLog::max('id') + 1;
            $ins_logs = new BankInstallmentLog();
            $ins_logs->transaction_id = 'BIP-' . $ins_log_max_id;
            $ins_logs->bank_id = $request->bank_id;
            $ins_logs->installment_info_id = $request->installment_info_id;
            $ins_logs->posting_date = date('Y-m-d');
            $ins_logs->paid_amount = $request->paid_amount;
            $ins_logs->installment_paid = $request->installment_paid;
            $ins_logs->payment_mode = $request->payment_mode;
            $ins_logs->cheque_no = $request->cheque_no;
            $ins_logs->cheque_date = $cheque_date;
            $ins_logs->file = $com_file_names;
            $ins_logs->description = $request->description;
            $ins_logs->user_id = $user_data->id;
            $ins_logs->branchId = $user_data->branchId;

            //save in bank_statements table
            if ($request->payment_mode == 'Bank') {
                //checking bank balance
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                if ($request->paid_amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank');
                }


                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'BIP-' . $ins_log_max_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Bank Installment Payment - Bank Name: ' . $bank_info->bank_name . ' ,A/C no: ' . $bank_info->account_no . ", " . $request->description;
                $b_statement->table_name = 'bank_installment_logs';
                $b_statement->debit = $request->paid_amount;
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $current_bal = $bank_info->balance() - $request->paid_amount;
                $b_statement->balance = $current_bal;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $user_data->branchId;
                $b_statement->user_id = $user_data->id;
                $b_statement->save();
            }

            //save in cash statements
            if ($request->payment_mode == 'Cash') {

                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->paid_amount > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                //save data in cash_statements
                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'BIP-' . $ins_log_max_id;
                $c_statement->posting_date = date('Y-m-d');
                $b_info = DB::table('bank_infos')->where('id', $request->bank_id)->first();
                $c_statement->description = 'Bank Installment Payment - Bank Name: ' . $b_info->bank_name . ', A/C no: ' . $b_info->account_no . ", " . $request->get('description');
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->table_name = 'bank_installment_logs';
                $c_statement->debit = $request->paid_amount;
                $c_statement->balance = $cash_bal - $request->paid_amount;
                $c_statement->branchId = $user_data->branchId;
                $c_statement->user_id = $user_data->id;
                $c_statement->save();
            }

            //update bank_installment_infos
            $ins_infos = BankInstallmentInfo::where('id', $request->installment_info_id)->first();
            $data = array();
            $data['installment_paid'] = $ins_infos->installment_paid + $request->installment_paid;
            $data['total_loan_paid'] = $ins_infos->total_loan_paid + $request->paid_amount;
            $ins_infos->update($data);

            //save in main installment logs table
            $status = $ins_logs->save();

            if($status) {
                DB::commit();
                Session::flash('message', 'Data Saved Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollback();
                Session::flash('message', 'Saving Data failed!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back()->withInput();
            }

        } catch(\Exception $e){
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function viewInstallmentPayment(Request $request, $installment_info_id)
    {
        if($request->date_range){
            $date_range = date_range_to_arr($request->date_range);
        }
        $installment_logs  = BankInstallmentLog::where('installment_info_id', $installment_info_id);

        if(isset($date_range)) {
            $installment_logs = $installment_logs->whereBetween('posting_date', $date_range);
        }

        if($request->search_text) {
            $installment_logs= $installment_logs->where(function($query) use ($request) {
                $query->where('payment_mode', 'LIKE', '%'.$request->input('search_name').'%')
                    ->orWhere('cheque_no', 'like', '%'.$request->input('search_name').'%')
                    ->orWhere('description', 'like', '%'.$request->input('search_name').'%')
                    ->orWhere('cheque_date', '=', $request->input('search_name'))
                    ->orWhere('paid_amount', '=', $request->input('search_name'));
            });
        }
        $installment_logs = $installment_logs->orderBy('id','DESC')->get();

        $installment_infos = BankInstallmentInfo::where('id',$installment_info_id)->first();
        return view('admin.bank.view_installment_payment',compact('installment_logs','installment_infos'));
    }

    public function deleteInstallmentPayment($tran_id)
    {
        $row = BankInstallmentLog::where('transaction_id',$tran_id)->first();

        DB::beginTransaction();
        try {
            if ($row->payment_mode == "Bank") {
                BankStatement::where('transaction_id', $tran_id)->delete();
            }

            if ($row->payment_mode == "Cash") {
                CashStatement::where('transaction_id', $tran_id)->delete();
            }

            //delete from expense table
            Expense::where('transaction_id', $tran_id)->delete();

            //update installment info table
            $installment_info_row = BankInstallmentInfo::where('id', $row->installment_info_id)->first();
            $paid_amt = $installment_info_row->total_loan_paid - $row->paid_amount;
            $ins_paid = $installment_info_row->installment_paid - $row->installment_paid;
            $installment_info_row->update(['total_loan_paid' => $paid_amt, 'installment_paid' => $ins_paid]);

            //delete files
            $file_text = $row->file;
            if ($file_text != "") {
                $files = explode(",", $file_text);
                foreach ($files as $file) {
                    if (is_dir(public_path())) {
                        unlink(public_path('img/files/pay_installment_files/' . $file));
                    } else {
                        unlink(base_path('img/files/pay_installment_files/' . $file));
                    }
                }
            }
            //finally delete installmentPaymentLogs
            $status = $row->delete();

            if($status)
            {
                DB::commit();
                Session::flash('message', 'Deleted Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollback();
                Session::flash('message', 'Deleted Data failed!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back();
            }
        }catch (\Exception $e){
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }
}


