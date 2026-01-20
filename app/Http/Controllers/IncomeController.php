<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\BankStatement;
use App\Models\CashStatement;
use App\Models\Branch;
use App\Models\BankInfo;
use App\Models\IncomeType;
use App\Models\Income;

class IncomeController extends Controller
{
    public function generalIncomeForm()
    {
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches  = Branch::orderBy('name')->get();
        $types = IncomeType::where('category','like','%General Income%')->orderBy('type_name')->get();

        return view('admin.income.add_general_income',compact('types', 'banks', 'branches'));
    }

    public function viewGeneralIncome(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->branchId??$user->branchId;

        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        }elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $incomes  = Income::where('transaction_id','LIKE', '%GI%');

        if(isset($date_range)) {
            $incomes = $incomes->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($branchId == 'head_office'){
            $incomes = $incomes->where('branchId', null);
        } elseif ($branchId != ''){
            $incomes = $incomes->where('branchId', $branchId);
        }

        if($request->search_text) {
            $incomes = $incomes->where(function($query) use ($request) {
                $query->where('income_name', 'LIKE', '%'. $request->search_text.'%')
                    ->orWhere('payment_mode', 'LIKE', '%'. $request->search_text.'%')
                    ->orWhere('amount', 'LIKE', '%'. $request->search_text.'%')
                    ->orWhereHas('income_type', function($q) use ($request){
                        $q->where('type_name', 'LIKE', '%'.$request->search_text.'%');
                    })->orWhereHas('branch', function($q) use ($request) {
                        $q->where('name', 'LIKE', '%'.$request->search_text.'%');
                    });
            });

        }

        $incomes = $incomes->orderBy('date','DESC')->get();
        $branches = Branch::orderBy('name')->get();
        $income_types = IncomeType::where('category','General Income')->get();
        return view('admin.income.view_general_income',compact('incomes','branches','income_types'));
    }

    public function saveGeneralIncome(Request $request)
    {
        $rules = [
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'income_type_id' => 'required|numeric',
            'income_name' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);

        $user = Auth::user();
        $date = date('Y-m-d',strtotime($request->date));
        $cheque_date = date('Y-m-d',strtotime($request->cheque_date));

        DB::beginTransaction();
        try {
            $income_id = Income::max('id');
            if ($income_id == "") $income_id = 1; else$income_id++;

            //SAVE UPLOADED FILES
            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    //$file_name = 'product-'.time().'.'.$ext;
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;
                    $destinationPath = "";
                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/income_files/general/');
                    } else {
                        $destinationPath = base_path('img/files/income_files/general/');
                    }

                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }
            }
            $com_file_names = implode(',', $file_names);

            $income = new Income();
            $income->transaction_id = 'GI-' . $income_id;
            $income->income_name = $request->income_name;
            $income->description = $request->description;
            $income->file = $com_file_names;
            $income->income_type_id = $request->income_type_id;
            $income->date = $date;
            $income->payment_mode = $request->payment_mode;
            $income->amount = $request->amount;
            $income->user_id = $user->id;
            $income->branchId = $user->branchId;
            $status = $income->save();

            //save in cash or bank statement
            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();

                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'GI-' . $income_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'General Income - ' . $request->income_name . ", " . $request->description;
                $b_statement->table_name = 'incomes';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->credit = $request->amount;
                $b_statement->balance = $bank_bal + $request->amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $user->branchId;
                $b_statement->user_id = $user->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {
                $cash_bal = cashBalance($request->branchId);

                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'GI-' . $income_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'General Income - ' . $request->income_name . ", " . $request->description;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->table_name = 'incomes';
                $c_statement->credit = $request->amount;
                $c_statement->balance = $cash_bal + $request->amount;
                $c_statement->branchId = $user->branchId;
                $c_statement->user_id = $user->id;
                $c_statement->save();
            }

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

    public function updateIncome(Request $request)
    {
        $rules = [
            'income_type_id' => 'required|numeric',
            'income_name' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
        ];
        $this->validate($request, $rules);
        $date = date('Y-m-d',strtotime($request->date));

        DB::beginTransaction();
        try {
            $income = Income::find($request->id);
            $income->income_name = $request->income_name;
            $income->income_type_id = $request->income_type_id;
            $income->date = $date;
            $income->amount = $request->amount;
            $income->income_name = $request->income_name;
            $income->description = $request->description;
            $status = $income->save();


            if ($income->payment_mode == 'Cash') {
                $c_statement = CashStatement::where('transaction_id', $income->transaction_id)->first();
                $c_statement->description = $request->description;
                $c_statement->ref_date = $date;
                $c_statement->credit = $request->amount;
                $c_statement->save();
            }

            if ($income->payment_mode == 'Bank') {
                $b_statement = BankStatement::where('transaction_id', $income->transaction_id)->first();
                $b_statement->description = $request->description;
                $b_statement->ref_date = $date;
                $b_statement->credit = $request->amount;
                $b_statement->save();
            }


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

    public function deleteIncome($tran_id)
    {
        $income = Income::where('transaction_id',$tran_id)->first();

        DB::beginTransaction();
        try {
            if ($income->payment_mode == "Bank") {
                BankStatement::where('transaction_id', $tran_id)->delete();
            }

            if ($income->payment_mode == "Cash") {
                CashStatement::where('transaction_id', $tran_id)->delete();
            }

            //delete files
            $file_text = $income->file;
            if ($file_text != "") {
                $files = explode(",", $file_text);
                foreach ($files as $file) {
                    if (is_dir(public_path())) {
                        unlink(public_path('img/files/income_files/general/' . $file));
                    } else {
                        unlink(base_path('img/files/income_files/general/' . $file));
                    }
                }
            }
            $status = $income->delete();

            if($status)
            {
                DB::commit();
                Session::flash('message', 'Data Deleted Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollback();
                Session::flash('message', 'Deleting Data failed!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function viewWasteIncome(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->branchId??$user->branchId;

        if($request->filled('date_range')){
            $date_range = date_range_to_arr($request->date_range);
        }elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
        }

        $incomes  = Income::where('transaction_id','LIKE', '%WI%');

        if(isset($date_range)) {
            $incomes = $incomes->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if($branchId == 'head_office'){
            $incomes = $incomes->where('branchId', null);
        } elseif ($branchId != ''){
            $incomes = $incomes->where('branchId', $branchId);
        }

        if($request->search_text) {
            $incomes = $incomes->where(function($query) use ($request) {
                $query->where('income_name', 'LIKE', '%'. $request->search_text.'%')
                    ->orWhere('payment_mode', 'LIKE', '%'. $request->search_text.'%')
                    ->orWhere('amount', 'LIKE', '%'. $request->search_text.'%')
                    ->orWhereHas('income_type', function($q) use ($request){
                        $q->where('type_name', 'LIKE', '%'.$request->search_text.'%');
                    })->orWhereHas('branch', function($q) use ($request) {
                        $q->where('name', 'LIKE', '%'.$request->search_text.'%');
                    });
            });

        }

        $incomes = $incomes->orderBy('date','DESC')->get();
        $branches = Branch::orderBy('name')->get();
        $income_types = IncomeType::where('category','Waste Income')->get();
        return view('admin.income.view_waste_income',compact('incomes','branches','income_types'));
    }

    public function wasteIncomeForm()
    {
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches = Branch::orderBy('name')->get();
        $types = IncomeType::where('category','like','%Waste Income%')->orderBy('type_name')->get();

        return view('admin.income.add_waste_income',compact('types', 'banks', 'branches'));
    }

    public function saveWasteIncome(Request $request)
    {
        $rules = [
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'income_type_id' => 'required|numeric',
            'income_name' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);

        $user = Auth::user();
        $date = date('Y-m-d',strtotime($request->date));
        $cheque_date = date('Y-m-d',strtotime($request->cheque_date));

        DB::beginTransaction();
        try {
            $income_id = Income::max('id');
            if ($income_id == "") $income_id = 1; else$income_id++;

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
                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/income_files/waste/');
                    } else {
                        $destinationPath = base_path('img/files/income_files/waste/');
                    }

                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }
            }
            $com_file_names = implode(',', $file_names);

            $income = new Income();
            $income->transaction_id = 'WI-' . $income_id;
            $income->income_name = $request->income_name;
            $income->description = $request->description;
            $income->file = $com_file_names;
            $income->income_type_id = $request->income_type_id;
            $income->date = $date;
            $income->payment_mode = $request->payment_mode;
            $income->amount = $request->amount;
            $income->user_id = $user->id;
            $income->branchId = $request->branchId;
            $status = $income->save();

            //save in cash or bank statement
            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();

                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'WI-' . $income_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Waste Income - ' . $request->income_name . ", " . $request->description;
                $b_statement->table_name = 'incomes';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->credit = $request->amount;
                $b_statement->balance = $bank_bal + $request->amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $user->branchId;
                $b_statement->user_id = $user->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {
                $cash_bal = cashBalance($request->branchId);

                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'WI-' . $income_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Waste Income - ' . $request->income_name . ", " . $request->description;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->table_name = 'incomes';
                $c_statement->credit = $request->amount;
                $c_statement->balance = $cash_bal + $request->amount;
                $c_statement->branchId = $user->branchId;
                $c_statement->user_id = $user->id;
                $c_statement->save();
            }

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



    public function incomeType()
    {
        $types = IncomeType::where('category','like','%General Income%')
            ->orWhere('category','like','%Waste Income%')
            ->get();
        return view('admin.income.income_type',compact('types'));
    }

    public function saveIncomeType(Request $request)
    {
        $rules = [
            'category' => 'required',
            'name' => 'required',
        ];

        $this->validate($request, $rules);
        $user = Auth::user();

        $income_type = new IncomeType();
        $income_type->type_name= $request->name;
        $income_type->category= $request->category;
        $income_type->description = $request->description;
        $income_type->user_id = $user->id;
        $status  = $income_type->save();

        if($status) {
            Session::flash('message', 'Added Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function updateIncomeType(Request $request)
    {
        $rules = [
            'category' =>'required',
            'name' =>'required'
        ];
        $this->validate($request, $rules);

        $income_type = IncomeType::findOrFail($request->id);

        $income_type->type_name = $request->name;
        $income_type->category = $request->category;
        $income_type->description = $request->description;
        $status = $income_type->save();

        if($status)
        {
            Session::flash('message', 'Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Updating Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function deleteIncomeType($id)
    {
//        return view('admin.income.add_income_type');
    }

}