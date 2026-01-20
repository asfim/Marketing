<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Branch;
//use App\Models\ProductPurchase;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Session;
//use Illuminate\Support\Facades\DB;
//use App\Models\BankStatement;
//use App\Models\CashStatement;
//use App\Models\BankInfo;
//use App\Models\Customer;
//use App\Models\ExpenseType;
//use App\Models\Expense;
//use App\Models\EngineerTipsStatement;
//use Carbon\Carbon;
//use Exception;
//
//class ExpenseController extends Controller
//{
//    public function viewGeneralExpense(Request $request)
//    {
//        $user = Auth::user();
//        $branchId = $request->branchId ?? $user->branchId;
//
//        if ($request->date_range) {
//            $date_range = date_range_to_arr($request->date_range);
//        } elseif ($request->date_range == '' && $request->search_text == '') {
//            $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
//        }
//
//        $expensesQuery = Expense::where('transaction_id', 'LIKE', '%GE%');
//
//        if (isset($date_range)) {
//            $expensesQuery->whereBetween('date', $date_range);
//        }
//
//        if ($branchId == 'head_office') {
//            $expensesQuery->where('branchId', null);
//        } elseif ($branchId != '') {
//            $expensesQuery->where('branchId', $branchId);
//        }
//// Fetch Truck Rent and Unload Bill Expenses with Filters
//        $truckRentQuery = ProductPurchase::where('truck_rent', '>', 0)->with('branch');
//        $unloadBillQuery = ProductPurchase::where('unload_bill', '>', 0)->with('branch');
//
//        if ($request->search_text) {
//            $expensesQuery->where(function ($query) use ($request) {
//                $query->where('expense_name', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhere('payment_mode', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhere('amount', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhereHas('expense_type', function ($q) use ($request) {
//                        $q->where('type_name', 'LIKE', '%' . $request->search_text . '%');
//                    })->orWhereHas('branch', function ($q) use ($request) {
//                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
//                    });
//            });
//
//            // Apply Search Filter to Truck Rent and Unload Bill Queries
//            $truckRentQuery->where(function ($query) use ($request) {
//                $query->where('transaction_id', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhere('truck_rent', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhereHas('branch', function ($q) use ($request) {
//                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
//                    });
//            });
//
//            $unloadBillQuery->where(function ($query) use ($request) {
//                $query->where('transaction_id', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhere('unload_bill', 'LIKE', '%' . $request->search_text . '%')
//                    ->orWhereHas('branch', function ($q) use ($request) {
//                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
//                    });
//            });
//        }
//
//// Fetch the filtered data
//        $truckRentOfProductPurchase = $truckRentQuery->get();
//        $unloadBillRentOfProductPurchase = $unloadBillQuery->get();
//
//
//        $expenses = $expensesQuery->orWhere('expense_name', 'Engineer Tips')
//            ->orderBy('id', 'DESC')
//            ->get()
//            ->toArray(); // Convert expenses to an array
//
//
//
//        // Convert Truck Rent to Array
//        $mappedTruckRent = $truckRentOfProductPurchase->map(function ($purchase) {
//            return [
//                'transaction_id' => $purchase->transaction_id,
//                'expense_name' => 'Truck Rent',
//                'expense_type_id' => 'Truck Rent',
//                'date' => $purchase->purchase_date,
//                'amount' => $purchase->truck_rent,
//                'description' => $purchase->description,
//                'branch' => $purchase->branch ? ['name' => $purchase->branch->name] : ['name' => '-'],
//                'file' => '',
//            ];
//        })->toArray(); // Convert collection to array
//
//        // Convert Unload Bill to Array
//        $mappedUnloadBill = $unloadBillRentOfProductPurchase->map(function ($purchase) {
//            return [
//                'transaction_id' => $purchase->transaction_id,
//                'expense_name' => 'Unload Bill',
//                'expense_type_id' => 'Unload Bill',
//                'date' => $purchase->purchase_date,
//                'amount' => $purchase->unload_bill,
//                'description' => $purchase->description,
//                'branch' => $purchase->branch ? ['name' => $purchase->branch->name] : ['name' => '-'],
//                'file' => '',
//            ];
//        })->toArray(); // Convert collection to array
//
//        // Merge all expenses into a single array
//        $expenses = array_merge($expenses, $mappedTruckRent, $mappedUnloadBill);
//
//        $branches = Branch::orderBy('name')->get();
//        $expense_types = ExpenseType::where('category', '!=', 'Production Expense')->get();
////dd($expenses);
//        return view('admin.expense.view_general_expense', [
//            'branches' => $branches,
//            'expenses' => $expenses,
//            'expense_types' => $expense_types
//        ]);
//    }
//


namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ProductPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\BankStatement;
use App\Models\CashStatement;
use App\Models\BankInfo;
use App\Models\Customer;
use App\Models\ExpenseType;
use App\Models\Expense;
use App\Models\EngineerTipsStatement;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;


class ExpenseController extends Controller
{
   public function viewGeneralExpense(Request $request)
{
    $user = Auth::user();
    $branchId = $request->branchId ?? $user->branchId;


    if ($request->filled('date_range')) {
        $date_range = date_range_to_arr($request->date_range);
    } elseif ($request->date_range == '') {
        $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
    }

    $expenses = Expense::with('expense_type', 'branch', 'engineer_tips_statements')
        ->whereHas('expense_type', function ($q) {
            $q->where('category', '!=', 'Production Expense');
        });

    if (isset($date_range)) {
        $expenses->whereBetween('date', [
            Carbon::parse($date_range[0])->format('Y-m-d'),
            Carbon::parse($date_range[1])->format('Y-m-d')
        ]);
    }

    if ($request->customer_id) {
        $expenses->whereHas('engineer_tips_statements', function ($q) use ($request) {
            $q->where('customer_id', $request->customer_id);
        });
    }

    if ($branchId == 'head_office') {
        $expenses = $expenses->where('branchId', null);
    } elseif ($branchId != '') {
        $expenses = $expenses->where('branchId', $branchId);
    }

    if ($request->search_text) {
        $expenses = $expenses->where(function ($query) use ($request) {
            $query->where('expense_name', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('payment_mode', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('amount', 'LIKE', '%' . $request->search_text . '%')
                ->orWhereHas('expense_type', function ($q) use ($request) {
                    $q->where('type_name', 'LIKE', '%' . $request->search_text . '%');
                })->orWhereHas('branch', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search_text . '%');
                });
        });
    }

    $expenses = $expenses->orderBy('date', 'DESC')->get();

    $branches = Branch::orderBy('name')->get();
    $expense_types = ExpenseType::where('category', '!=', 'Production Expense')->get();
    $customers = Customer::all();

    return view('admin.expense.view_general_expense', compact('customers','branches', 'expenses', 'expense_types'));
}

    public function generalExpenseForm()
    {
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches = Branch::orderBy('name')->get();
        $types = ExpenseType::where('category', 'like', '%General Expense%')->orderBy('type_name', 'ASC')->get();
        $customers = Customer::all();

        return view('admin.expense.add_general_expense', compact('types', 'banks', 'branches', 'customers'));
    }

    public function saveGeneralExpense(Request $request)
    {
        $rules = [
            'branchId' => 'nullable|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'expense_type_id' => 'required|numeric',
            'expense_name' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt',
            'customer_id' => 'required_if:expense_type_id,==,113',
        ];

        $this->validate($request, $rules);
        $user = Auth::user();
        $date = date('Y-m-d', strtotime($request->date));
        $cheque_date = date('Y-m-d', strtotime($request->cheque_date));

        DB::beginTransaction();
        try {
            $ex_id = Expense::max('id');
            if ($ex_id == "") $ex_id = 1;
            else $ex_id++;

            //save files in server
            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;
                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/expense_files/general/');
                    } else {
                        $destinationPath = base_path('img/files/expense_files/general/');
                    }

                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }
            }
            $com_file_names = implode(',', $file_names);

            $expense = new Expense();
            $expense->transaction_id = 'GE-' . $ex_id;
            $expense->expense_name = $request->expense_name;
            $expense->description = $request->description;
            $expense->expense_type_id = $request->expense_type_id;
            $expense->date = $date;
            $expense->amount = $request->amount;
            $expense->payment_mode = $request->payment_mode;
            $expense->file = $com_file_names;
            $expense->branchId = $request->branchId;
            $expense->user_id = $user->id;
            $expense->expense_type_gen_pur = 0;

            $status = $expense->save();

            ///insert into engineer_tips_statements
            if ($request->expense_type_id == 113) {
                $e_statements = new EngineerTipsStatement();
                $e_statements->transaction_id = 'GE-' . $ex_id;

                // Convert the date to the correct format
                $e_statements->posting_date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
                $e_statements->customer_name = DB::table('customers')->where('id', $request->customer_id)->value('name');
                $e_statements->description = $request->get('description');
                $e_statements->table_name = 'expences';
                $e_statements->adjustment = $request->adjustment;
                $e_statements->credit = $request->amount;
                $e_statements->customer_id = $request->get('customer_id');
                $e_statements->user_id = $user->id;
                $e_statements->branchId = $request->branchId;

                $id_no = EngineerTipsStatement::max('id');
                if ($id_no == "") {
                    $pre_balance = 0;
                    $current_bal = $pre_balance - $request->amount;
                    $e_statements->balance = $current_bal;
                } else {
                    $pre_balance = DB::table('engineer_tips_statements')->orderBy('id', 'desc')->first();
                    $current_bal = $pre_balance->balance - $request->amount - $request->adjustment;
                    $e_statements->balance = $current_bal;
                }


             //   Log::info('EngineerTipsStatement about to be saved', $e_statements->toArray());

                try {
                    $e_statements->save();
                } catch (Exception $ex) {
                //    Log::error('Error saving EngineerTipsStatement: ' . $ex->getMessage());
                    Session::put('message', $ex->getMessage());
                }
            }


            //save in cash or bank statement
            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                //CHECKING BANK BALANCE
                if ($request->amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank!');
                }

                //save data in bank_statements
                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'GE-' . $ex_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'General Expense - ' . $request->expense_name . ", " . $request->description;
                $b_statement->table_name = 'expenses';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->debit = $request->amount;
                $b_statement->balance = $bank_bal - $request->amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $request->branchId;
                $b_statement->user_id = $user->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {
                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->amount > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                //save data in cash_statements
                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'GE-' . $ex_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'General Expense - ' . $request->expense_name . ", " . $request->description;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->table_name = 'expenses';
                $c_statement->debit = $request->amount;
                $c_statement->balance = $cash_bal - $request->amount;
                $c_statement->branchId = $request->branchId;
                $c_statement->user_id = $user->id;
                $c_statement->save();
            }

            if ($status) {
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
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function viewProductExpense(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->branchId ?? $user->branchId;
        if ($request->date_range) {
            $date_range = date_range_to_arr($request->date_range);
        }
                elseif ($request->date_range == '' && $request->search_text == '') {
           $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
       }


        $expenses = Expense::where('transaction_id', 'LIKE', '%PE%');

        if (isset($date_range)) {
            $expenses = $expenses->whereBetween('date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if ($branchId == 'head_office') {
            $expenses = $expenses->where('branchId', null);
        } elseif ($branchId != '') {
            $expenses = $expenses->where('branchId', $branchId);
        }

        if ($request->search_text) {
            $expenses = $expenses->where(function ($query) use ($request) {
                $query->where('expense_name', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('payment_mode', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('amount', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhereHas('expense_type', function ($q) use ($request) {
                        $q->where('type_name', 'LIKE', '%' . $request->search_text . '%');
                    })->orWhereHas('branch', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
                    });
            });
        }

        $branches = Branch::orderBy('name')->get();
        $expenses = $expenses->orderBy('date', 'DESC')->get();
        $expense_types = ExpenseType::where('category', 'Production Expense')->get();

        return view('admin.expense.view_product_expense', compact('expenses', 'expense_types', 'branches'));
    }

    public function productionExpForm()
    {
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches = Branch::orderBy('name')->get();
        $types = ExpenseType::where('category', 'like', '%Production Expense%')->orderBy('type_name', 'ASC')->get();

        return view('admin.expense.add_product_expense', compact('types', 'banks', 'branches'));
    }

    public function saveProductionExpense(Request $request)
    {
        $rules = [
            'branchId' => 'nullable|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'expense_type_id' => 'required|numeric',
            'expense_name' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'file.*' => 'mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);

        $date = date('Y-m-d', strtotime($request->date));
        $cheque_date = date('Y-m-d', strtotime($request->cheque_date));
        DB::beginTransaction();
        try {
            $ex_id = Expense::max('id');
            if ($ex_id == "") $ex_id = 1;
            else $ex_id++;

            //save files in server
            $file_names = array();
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach ($files as $file) {
                    $ext = $file->extension();
                    $original_file_name = $file->getClientOriginalName();
                    $ex_file_name = explode('.', $original_file_name);
                    $file_name = $ex_file_name[0] . '-' . rand(1, 1500000) . '.' . $ext;
                    if (is_dir(public_path())) {
                        $destinationPath = public_path('img/files/expense_files/general/');
                    } else {
                        $destinationPath = base_path('img/files/expense_files/general/');
                    }

                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }
            }
            $com_file_names = implode(',', $file_names);

            $user = Auth::user();
            $expense = new Expense();
            $expense->transaction_id = 'PE-' . $ex_id;
            $expense->expense_name = $request->expense_name;
            $expense->description = $request->description;
            $expense->expense_type_id = $request->expense_type_id;
            $expense->date = $date;
            $expense->amount = $request->amount;
            $expense->payment_mode = $request->payment_mode;
            $expense->file = $com_file_names;
            $expense->branchId = $request->branchId;
            $expense->user_id = $user->id;
            $status = $expense->save();


            //save in cash or bank statement
            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                //CHECKING BANK BALANCE
                if ($request->amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank!');
                }

                //save data in bank_statements
                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'PE-' . $ex_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Production Expense - ' . $request->expense_name . ", " . $request->description;
                $b_statement->table_name = 'expenses';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->debit = $request->amount;
                $b_statement->balance = $bank_bal - $request->amount;;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $request->branchId;
                $b_statement->user_id = $user->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {
                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->amount > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                //save data in cash_statements
                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'PE-' . $ex_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Production Expense - ' . $request->expense_name . ", " . $request->description;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->table_name = 'expenses';
                $c_statement->debit = $request->amount;
                $c_statement->balance = $cash_bal - $request->amount;
                $c_statement->branchId = $request->branchId;
                $c_statement->user_id = $user->id;
                $c_statement->save();
            }

            if ($status) {
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
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function updateExpense(Request $request)
    {

        $rules = [
            'expense_type_id' => 'required|numeric',
            'expense_name' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
        ];
        $this->validate($request, $rules);
        $date = date('Y-m-d', strtotime($request->date));
        DB::beginTransaction();
        try {


            $expense = Expense::find($request->id);
            $expense->expense_name = $request->expense_name;
            $expense->description = $request->description;
            $expense->expense_type_id = $request->expense_type_id;
            $expense->date = $request->date;
            $expense->amount = $request->amount;
            $status = $expense->save();

            if ($request->expense_type_id == 113) {
                $customer_id = $request->customer_id;
                $eng_tips = EngineerTipsStatement::where('transaction_id', $expense->transaction_id)->first();
                if ($eng_tips) {
                    $eng_tips->customer_id = $customer_id;
                   $eng_tips->customer_name = Customer::find($customer_id)->name;
                    $eng_tips->save();
                }
            }


            if ($expense->payment_mode == 'Bank') {
                $b_statement = BankStatement::where('transaction_id', $expense->transaction_id)->first();
                $b_statement->description = $request->description;
                $b_statement->ref_date = $date;
                $b_statement->debit = $request->amount;
                $b_statement->save();
            }

            if ($expense->payment_mode == 'Cash') {
                $c_statement = CashStatement::where('transaction_id', $expense->transaction_id)->first();
                $c_statement->description = $request->description;
                $c_statement->ref_date = $request->date;
                $c_statement->debit = $request->amount;
                $c_statement->save();
            }

            if ($status) {
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
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function deleteExpense($tran_id)
    {
        $expense = Expense::where('transaction_id', $tran_id)->first();

        DB::beginTransaction();
        try {
            if ($expense->payment_mode == "Bank") {
                BankStatement::where('transaction_id', $tran_id)->delete();
            }

            if ($expense->payment_mode == "Cash") {
                CashStatement::where('transaction_id', $tran_id)->delete();
            }

            //delete files
            $file_text = $expense->file;
            if ($file_text != "") {
                $files = explode(",", $file_text);
                foreach ($files as $file) {
                    if (is_dir(public_path())) {
                        unlink(public_path('img/files/expense_files/general/' . $file));
                    } else {
                        unlink(base_path('img/files/expense_files/general/' . $file));
                    }
                }
            }

            $status = $expense->delete();

            if ($status) {
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

    public function loadEngineerTips(Request $request)
    {
        $customer_id = $request->customer_id;
        $tips_rows = EngineerTipsStatement::where('customer_id', $customer_id)->get();
        $balance = 0;
        $total_exp_tips = 0;
        foreach ($tips_rows as $tips_row) {
            $balance += $tips_row->debit - $tips_row->credit - $tips_row->adjustment;
        }

        $response = array(
            'balance' => $balance . 'Tk',

        );
        return response()->json($response);
    }

    public function viewEngineerTips(Request $request)
    {
        $bal_f = 0;
        $date_info = "";
        $customer_name = "";
        $customer_address = "";
        $engineertipsObj = new EngineerTipsStatement();

        $search_text = $request->input('search_name');
        $src_from_date = $request->input('from_date');
        $src_to_date = $request->input('to_date');

        if ($search_text != "" || ($src_from_date != "" && $src_to_date != "")) {
            $from_date = date("Y-m-d", strtotime($request->input('from_date')));
            $to_date = date("Y-m-d", strtotime($request->input('to_date')));

            if ($search_text != "" && $src_from_date != "" && $src_to_date != "") {
                $customer_name = $request->input('search_name');
                $customer_address = DB::table('customers')->where('name', $request->get("search_name"))->value('address');
                $all_statements = $engineertipsObj->whereBetween('posting_date', [$from_date, $to_date])
                    ->where(function ($query) use ($request) {
                        $query->where('transaction_id', 'LIKE', '%' . $request->input('search_name') . '%')
                            ->orwhere('description', '=', $request->input('search_name'))
                            ->orwhere('debit', 'LIKE', '%' . $request->input('search_name') . '%')
                            ->orwhere('credit', 'LIKE', '%' . $request->input('search_name') . '%')
                            ->orwhere('balance', 'LIKE', '%' . $request->input('search_name') . '%')
                            ->orWhereHas('customers', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->input('search_name') . '%');
                            });
                    })->orderBy('id', 'DESC')->get();
            } elseif ($search_text == "" && $src_from_date != "" && $src_to_date != "") {

                $all_statements = $engineertipsObj->whereBetween('posting_date', [$from_date, $to_date])->orderBy('id', 'DESC')->get();
            } elseif ($search_text != "" && $src_from_date == "" && $src_to_date == "") {

                $customer_name = $request->input('search_name');
                $customer_address = DB::table('customers')->where('name', $request->get("search_name"))->value('address');
                $all_statements = $engineertipsObj->where(function ($query) use ($request) {
                    $query->where('transaction_id', 'LIKE', '%' . $request->input('search_name') . '%')
                        ->orwhere('description', '=', $request->input('search_name'))
                        ->orwhere('debit', 'LIKE', '%' . $request->input('search_name') . '%')
                        ->orwhere('credit', 'LIKE', '%' . $request->input('search_name') . '%')
                        ->orwhere('balance', 'LIKE', '%' . $request->input('search_name') . '%')
                        ->orWhereHas('customers', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->input('search_name') . '%');
                        });
                })->orderBy('id', 'DESC')->get();
            }
        } else {
            $today = date('Y-m-d');
            $last_month = date('Y-m-d', strtotime('today - 30 days'));
            $all_statements = EngineerTipsStatement::whereBetween('posting_date', [$last_month, $today])->get();
            //return $all_statements;
        } ////else end

        return view('admin.expense.view_engineer_tips', compact('all_statements', 'cash_balance', 'customer_name', 'customer_address'));
    }


    public function expenseType()
    {
        $expense_types = ExpenseType::where('category', 'like', '%General Expense%')
            ->orWhere('category', 'like', '%Production Expense%')
            ->orderBy('id', 'DESC')->get();
        return view('admin.expense.expense_type', ['types' => $expense_types]);
    }

    public function saveExpenseType(Request $request)
    {
        $rules = [
            'category' => 'required',
            'name' => 'required',
        ];

        $this->validate($request, $rules);
        $user = Auth::user();

        $expense_type = new ExpenseType();
        $expense_type->type_name = $request->name;
        $expense_type->category = $request->category;
        $expense_type->description = $request->description;
        $expense_type->user_id = $user->id;
        $status = $expense_type->save();

        if ($status) {
            Session::flash('message', 'Added Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function updateExpenseType(Request $request)
    {
        $rules = [
            'category' => 'required',
            'name' => 'required'
        ];
        $this->validate($request, $rules);

        $expense_type = ExpenseType::findOrFail($request->id);

        $expense_type->type_name = $request->name;
        $expense_type->category = $request->category;
        $expense_type->description = $request->description;
        $status = $expense_type->save();

        if ($status) {
            Session::flash('message', 'Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Updating Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function deleteExpenseType()
    {
    }
}

