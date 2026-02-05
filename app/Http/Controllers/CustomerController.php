<?php

namespace App\Http\Controllers;

use App\Models\BankInfo;
use App\Models\BankStatement;
use App\Models\Branch;
use App\Models\CashStatement;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\CustomerProject;
use App\Models\CustomerStatement;
use App\Models\ProductSale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{


    public function updateBalance(Request $request)
    {
        foreach ($request->balances as $customerId => $debitValue) {
            if (!empty($debitValue)) {
                // Check if a record for the customer already exists
                $existingStatement = CustomerStatement::where('customer_id', $customerId)->first();

                if ($existingStatement) {
                    // Update the existing record
                    $existingStatement->update([
                        'transaction_id' => $existingStatement->transaction_id, // Keep the same transaction ID
                        'posting_date' => now()->toDateString(),
                        'description' => 'Balance',
                        'table_name' => 'customer_payments',
                        'debit' => $debitValue,
                        'credit' => 0,
                        'balance' => 0,
                        'user_id' => 1,
                        'branchId' => null,
                        'updated_at' => now(),
                    ]);
                } else {
                    // Insert a new record if no existing one found
                    $lastTransaction = CustomerStatement::latest('id')->first();
                    $nextTransactionId = $lastTransaction
                        ? 'opening_balance_' . ((int)str_replace('opening_balance_', '', $lastTransaction->transaction_id) + 1)
                        : 'opening_balance_1';

                    CustomerStatement::create([
                        'transaction_id' => $nextTransactionId,
                        'posting_date' => now()->toDateString(),
                        'description' => 'Balance',
                        'table_name' => 'customer_payments',
                        'debit' => $debitValue,
                        'credit' => 0,
                        'balance' => 0,
                        'customer_id' => $customerId,
                        'user_id' => '1',
                        'branchId' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Balances updated successfully');
    }


    public function index(Request $request)
    {
        $customers = new Customer();
        if ($request->search_text != "") {
            $customers = $customers->where('name', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('phone', 'LIKE', $request->search_text . '%')
                ->orWhere('extra_phone_no', 'LIKE', '%' . $request->search_text . '%')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $customers = $customers->orderBy('id', 'DESC')->get();
        }

        return view('admin.customer.customer_list', compact('customers'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required|unique:customers',
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $cutomers = new Customer();
        $cutomers->name = $request->name;
        $cutomers->email = $request->email;
        $cutomers->phone = $request->phone;
        $cutomers->address = $request->address;
        $cutomers->extra_phone_no = $request->extra_phone_no;
        $cutomers->user_id = $user_data->id;
        $status = $cutomers->save();

        if ($status) {
            Session::flash('message', 'Data Saved Successfully!');
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
            'name' => 'required',
            'phone' => 'required'
        ];

        $this->validate($request, $rules);

        $customer = Customer::find($request->id);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->extra_phone_no = $request->extra_phone_no;
        $status = $customer->save();

        if ($status) {
            Session::flash('message', 'Data Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Updating failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $branchId = $request->branchId ?? $user->branchId;
        $customer = Customer::findOrFail($id);
        $branches = Branch::all();
        $selected_tab = $request->tab_type ?? 'tab-profile';
        $generate_date_range=null;

        if ($request->challan_date_range) {
            $challan_date_range = date_range_to_arr($request->challan_date_range);
        } elseif ($request->challan_date_range == '' && $request->challan_search_text == '') {
            $challan_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        }
        if ($request->payment_date_range) {
            $payment_date_range = date_range_to_arr($request->payment_date_range);
        }

//        elseif ($request->payment_date_range == '' && $request->payment_search_text == '') {
//            $payment_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
//        }


        if ($request->statement_date_range) {
            $statement_date_range = date_range_to_arr($request->statement_date_range);
        }
//        elseif ($request->statement_date_range == '' && $request->statement_date_range == '') {
//            $statement_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
//        }

        //SEARCH ON CHALLAN TAB
        if ($selected_tab == 'tab-challan') {

            //  Use query builder
            $challans = $customer->challans();
            $mixDesigns = $customer->mixDesigns()->get();


            if (isset($challan_date_range)) {
                $challans->whereBetween('sell_date', [
                    Carbon::parse($challan_date_range[0])->format('Y-m-d'),
                    Carbon::parse($challan_date_range[1])->format('Y-m-d')
                ]);
            }

            if ($request->challan_status != '') {
                $challans->where('status', $request->challan_status);
            }

            if ($request->filled('select_challan_psi')) {
                $challans->whereHas('mix_design', function ($q) use ($request) {
                    $q->where('psi', $request->select_challan_psi);
                });
            }


            if ($request->challan_search_text != '') {
                $challans->where('challan_no', $request->challan_search_text);
            }

            //  Order by date descending and get
            $challans = $challans->orderByDesc('sell_date')->get();

        } else {
            // Also use query for consistency
            $challans = $customer->challans()
                ->whereBetween('sell_date', [
                    Carbon::parse($challan_date_range[0])->format('Y-m-d'),
                    Carbon::parse($challan_date_range[1])->format('Y-m-d')
                ])
                ->orderByDesc('sell_date')
                ->get();
        }


//        if($selected_tab == 'tab-challan'){
//            $challans = $customer->challans;
//
//            if(isset($challan_date_range)) {
//                $challans = $challans->whereBetween('sell_date', $challan_date_range);
//            }
//
//            if($request->challan_status != '') {
//                $challans = $challans->where('status', $request->challan_status);
//            }
//
//            if($request->search_text !='') {
//                $challans = $challans->where('challan_no', $request->search_text);
//            }
//        } else {
//            $challans = $customer->challans->whereBetween('sell_date', $challan_date_range);
//        }

        //SEARCH ON PAYMENT TAB

        if ($selected_tab == 'tab-payment') {

            // Use query builder, not collection
            $payments = $customer->payments();


            if (isset($payment_date_range)) {

//                dd($payment_date_range);
                $payments->whereBetween('ref_date',
                    [
                        Carbon::parse($payment_date_range[0])->format('Y-m-d'),
                        Carbon::parse($payment_date_range[1])->format('Y-m-d')
                    ]
                );
            }

            if ($request->payment_search_text) {
                $payments->where(function ($query) use ($request) {
                    $query->where('transaction_id', 'LIKE', '%' . $request->payment_search_text . '%')
                        ->orWhere('payment_mode', 'LIKE', '%' . $request->payment_search_text . '%')
                        ->orWhere('paid_amount', $request->payment_search_text)
                        ->orWhereHas('bank_info', function ($q) use ($request) {
                            $q->where('bank_name', 'LIKE', '%' . $request->payment_search_text . '%');
                        });
                });
            }

            // Add order and fetch
            $payments = $payments->orderByDesc('ref_date')->get();

        } else {
            $payments = $customer->payments()
//                ->whereBetween('ref_date', $payment_date_range)
                ->orderByDesc('ref_date')
                ->get();
        }




        //SEARCH ON STATEMENT TAB

        if ($selected_tab == 'tab-statement') {
            $statements = $customer->customerStatements();

            // Branch filter (as it is)
            if ($branchId == 'head_office') {
                $statements->where(function ($q) {
                    $q->whereNull('branchId')
                        ->orWhereNotNull('branchId');
                });
            } elseif ($branchId != '') {
                $statements->where(function ($q) use ($branchId) {
                    $q->where('branchId', $branchId)
                        ->orWhereNull('branchId');
                });
            }

            // Text search (as it is)
            if ($request->statement_search_text != '') {
                //   Log::info("Statement text search: " . $request->statement_search_text);
                $statements->where(function ($query) use ($request) {
                    $query->where('transaction_id', 'LIKE', '%' . $request->statement_search_text . '%')
                        ->orWhere('description', '=', $request->statement_search_text)
                        ->orWhere('debit', 'LIKE', '%' . $request->statement_search_text . '%')
                        ->orWhere('credit', 'LIKE', '%' . $request->statement_search_text . '%')
                        ->orWhere('balance', 'LIKE', '%' . $request->statement_search_text . '%')
                        ->orWhereHas('customer', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->statement_search_text . '%');
                        });
                });
            }

            if (isset($statement_date_range)) {

                $start = Carbon::parse($statement_date_range[0])->format('Y-m-d');
                $end = Carbon::parse($statement_date_range[1])->format('Y-m-d');

                // 1. Get all CBILL transactions (bills)
                $bill_ids = \App\Models\Bill::where('customer_id', $customer->id)
                    ->whereBetween('bill_date', [$start, $end])
                    ->get(); // keep as collection

                // 2. Get all CUSP transactions (payments)
                $payment_ids = \App\Models\CustomerPayment::where('customer_id', $customer->id)
                    ->whereBetween('ref_date', [$start, $end])
                    ->get();

                // 3. Get opening balance / other statements
                $opening_balance = \App\Models\CustomerStatement::where('customer_id', $customer->id)
                    ->whereBetween('posting_date', [$start, $end])
                    ->get();

                // 4. Merge all collections
                $all_ids = $bill_ids->merge($payment_ids)->merge($opening_balance);

                // 5. Sort by actual transaction date
                $all_ids = $all_ids->sort(function ($a, $b) {
                    $dateA = $a->bill_date ?? $a->ref_date ?? $a->posting_date ?? null;
                    $dateB = $b->bill_date ?? $b->ref_date ?? $b->posting_date ?? null;
                    return strtotime($dateA) <=> strtotime($dateB);
                })->values(); // reindex collection

                // 6. Extract only transaction IDs for filtering
                $all_transaction_ids = $all_ids->pluck('transaction_id')->toArray();

                // 7. Filter statements by transaction_ids
                if (!empty($all_transaction_ids)) {
                    $statements->whereIn('transaction_id', $all_transaction_ids);
                } else {
                    $statements->whereRaw('1=0'); // force empty
                }
            }

            // 8. Get final statements (keep original variables)
            $statements = $statements->get();


            //   Log::info("Final Statements Count: " . $statements->count());

        } else {

            $statements = $customer->customerStatements()
//                ->whereBetween('posting_date', $payment_date_range)
                ->orderByDesc('posting_date')
                ->get();

        }


        // DEMO BILL GENERATE WHICH WILL NOT AFFECT ON ANY TABLE
        if ($selected_tab == 'tab-demo') {
            $demo_date_range = date_range_to_arr($request->demo_date_range);


            $non_submitted_demo_challans = $customer->demoChallans->where('status', 1)
                ->when($request->has('project_id') && isset($request->project_id), function ($q) use ($request) {
                    return $q->where('project_id', $request->project_id);
                })->when($request->has('mix_design_id') && isset($request->mix_design_id), function ($q) use ($request) {
                    return $q->where('mix_design_id', '=', $request->mix_design_id);
                })
                ->when($request->filled('demo_date_range'), function ($q) use ($demo_date_range) {
                    return $q->whereBetween('sell_date', [
                        Carbon::parse($demo_date_range[0])->format('Y-m-d'),
                        Carbon::parse($demo_date_range[1])->format('Y-m-d')
                    ]);
                });

//            dd($non_submitted_demo_challans);
        } else {
           
            $non_submitted_demo_challans = [];
            
        }


        if ($selected_tab == 'tab-bill-generate') {


            if ($request->filled('generate_date_range')) {

                $generate_date_range = date_range_to_arr($request->generate_date_range);
                $generate_date_range = [Carbon::parse($generate_date_range[0])->format('Y-m-d'),
                    Carbon::parse($generate_date_range[1])->format('Y-m-d')
                ];
            } else {

                $generate_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
            }



            $non_submitted_challans = $customer->challans->where('status', 1)->whereBetween('sell_date', $generate_date_range)
                ->when($request->has('project_id') && isset($request->project_id), function ($q) use ($request) {
                    return $q->where('project_id', $request->project_id);
                })
                ->when($request->has('mix_design_id') && isset($request->mix_design_id), function ($q) use ($request) {

                    return $q->where('mix_design_id', '=', $request->mix_design_id);
                });


        } 
        else {
             $non_submitted_challans = [];
        }


        $total_bill = CustomerStatement::where('customer_id', $id)->sum('credit');
        $total_payment = CustomerPayment::where('customer_id', $id)->sum('paid_amount');
        $total_adjustment = CustomerPayment::where('customer_id', $id)->sum('adjustment_amount');

        $total_billable = 0;

        $uncheck_challans = ProductSale::where('customer_id', $id)->where('status', 1)->get();
        foreach ($uncheck_challans as $challan) {       
            $rate = ($challan->rate > 0) ? $challan->rate : $challan->mix_design->rate;

            $total_billable += ($challan->cuM * 35.315) * $rate;
        }


//dd($customer);
        return view('admin.customer.customer_profile', compact('customer', 'branches',
            'selected_tab', 'challans', 'non_submitted_challans', 'payments', 'statements',
            'total_bill', 'total_billable', 'total_payment', 'total_adjustment', 'non_submitted_demo_challans','generate_date_range'));
    }

//    public function destroy($id)
//    {
//        $tbl_cust = Customer::find($id);
//        $tbl_cust->delete();
//        Session::put(['message' => 'Deleted Successfully', 'alert' => 'alert-info']);
//        return Redirect::to('/view-customer');
//    }

    public function viewCustomerProjectForm($id)
    {
        $customer = Customer::find($id);
        return view('admin.customer.add_customer_project', compact('customer'));
    }

    public function saveCustomerProject(Request $request)
    {
        $rules = [
            'name' => 'required',
            'customer_id' => 'required',
            'address' => 'required'
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $cutomers = new CustomerProject();
        $cutomers->name = $request->get('name');
        $cutomers->customer_id = $request->get('customer_id');
        $cutomers->address = $request->get('address');
        $cutomers->user_id = $user_data->id;
        $status = $cutomers->save();

        if ($status) {
            Session::flash('message', 'Data Saved Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Saving Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function viewCustomerProject($id)
    {
        $customer_name = Customer::find($id)->value('name');
        $projects = CustomerProject::where('customer_id', $id)->orderBy('id', 'DESC')->get();
        return view('admin.customer.view_customer_project', compact('customer_name', 'projects'));
    }

    public function updateCustomerProject(Request $request)
    {
        $rules = [
            'name' => 'required',
            'id' => 'required',
            'address' => 'required'

        ];

        $this->validate($request, $rules);

        $customers = CustomerProject::find($request->id);
        $customers->name = $request->name;
        $customers->address = $request->address;

        $status = $customers->save();

        if ($status) {
            Session::flash('message', 'Data Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Updating Data failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function deleteCustomerProject($id)
    {
        $project = CustomerProject::find($id);

        if ($project->challans()->exists()) {
            Session::flash('message', 'Cann\'t delete! Challan record found with this project');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

        try {
            $project->delete();
            Session::flash('message', 'Project Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch (Exception $ex) {
            Session::flash('message', report($ex));
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function customerPaymentForm()
    {
        $customers = Customer::orderBy('name')->get();
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.customer.customer_payment', compact('customers', 'banks', 'branches'));
    }

    public function loadCustomerBalance(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        return response()->json(['balance' => $customer->balanceText()]);
    }

    public function saveCustomerPayment(Request $request)
    {
        $rules = [
            'customer_id' => 'required|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'paid_amount' => 'required|numeric|min:0',
            'adjustment_amount' => 'nullable|numeric',
            'file.*' => 'nullable|mimes:jpeg,bmp,png,doc,docx,pdf,xls,xlsx,xlsm,txt'
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();
        $customer = Customer::find($request->customer_id);
        $cheque_date = date('Y-m-d', strtotime($request->cheque_date));

        DB::beginTransaction();
        try {
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
                        $destinationPath = public_path('img/files/income_files/customer_payment/');
                    } else {
                        $destinationPath = base_path('img/files/income_files/customer_payment/');
                    }

                    $file->move($destinationPath, $file_name);
                    $file_names[] = $file_name;
                }
            }
            $com_file_names = implode(',', $file_names);

            //add data to customer_payment table
            $tran_no = CustomerPayment::max('id') + 1;
            $c_payment = new CustomerPayment();
            $c_payment->transaction_id = 'CUSP-' . $tran_no;
            $c_payment->customer_id = $request->customer_id;
            $c_payment->payment_date = date('Y-m-d');
            $c_payment->bill_no = $tran_no;
            $c_payment->payment_mode = $request->payment_mode;
            $c_payment->paid_amount = $request->paid_amount;
            $c_payment->adjustment_amount = $request->adjustment_amount ?? 0;
            $c_payment->description = $request->description;
            $c_payment->file = $com_file_names;
            $c_payment->user_id = $user_data->id;
            $c_payment->cheque_no = $request->cheque_no;
            $c_payment->ref_date = $cheque_date;
            $c_payment->bank_id = $request->bank_id;
            $c_payment->save();

            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                //save data in bank_statements
                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'CUSP-' . $tran_no;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Received from ' . $customer->name . ', ' . $request->description;
                $b_statement->table_name = 'customer_payments';
                $b_statement->ref_date = $cheque_date;
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->credit = $request->paid_amount;
                $b_statement->balance = $bank_info->balance() + $request->paid_amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $user_data->branchId;
                $b_statement->user_id = $user_data->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {
                //save data in cash_statements
                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'CUSP-' . $tran_no;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Received from ' . $customer->name . ', ' . $request->description;
                $c_statement->table_name = 'customer_payments';
                $c_statement->credit = $request->paid_amount;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->balance = cashBalance($request->branchId) + $request->paid_amount;
                $c_statement->branchId = $user_data->branchId;
                $c_statement->user_id = $user_data->id;
                $c_statement->save();
            }

            //save data in customer statements
            $cus_statement = new CustomerStatement();
            $cus_statement->transaction_id = 'CUSP-' . $tran_no;
            $cus_statement->posting_date = date('Y-m-d');
            $cus_statement->description = $request->description;
            $cus_statement->table_name = 'customer_payments';
            $cus_statement->debit = $request->paid_amount + $request->adjustment_amount;
            $cus_statement->customer_id = $request->customer_id;
            $current_bal = $customer->balance() - ($request->paid_amount + $request->adjustment_amount);
            $cus_statement->balance = $current_bal;
            $cus_statement->branchId = $user_data->branchId;
            $cus_statement->user_id = $user_data->id;
            $cus_statement->save();

            DB::commit();
            Session::flash('message', 'Data Saved Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

    }

    public function paymentDetails(Request $request)
    {
        if ($request->filled('date_range')) {
            $date_range = date_range_to_arr($request->get('date_range'));
        } 
        else{
            $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
        }

        $cus_payments = new CustomerPayment();

        if (isset($date_range)) {
            $cus_payments = $cus_payments->whereBetween('ref_date', [
                    Carbon::parse($date_range[0])->format('Y-m-d'),
                    Carbon::parse($date_range[1])->format('Y-m-d')]
            );
        }

        if ($request->search_text) {

            $cus_payments = $cus_payments->where(function ($query) use ($request) {
                $query->where('bill_no', 'LIKE', '%' . $request->search_text . '%')
                    ->orwhere('payment_mode', 'LIKE', '%' . $request->search_text . '%')
                    ->orwhere('transaction_id', 'LIKE', '%' . $request->search_text . '%')
//                    ->orwhere('paid_amount', '=', $request->search_text)
                    ->orWhereHas('customer', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
                    });
//                    ->orWhereHas('bank_info', function ($q) use ($request) {
//                        $q->where('bank_name', 'LIKE', '%' . $request->search_text . '%');
//                    });
            });

        }

        //  $cus_payments = $cus_payments->orderBy('id','DESC')->get();
        $cus_payments = $cus_payments->orderBy('ref_date', 'DESC')->get();

        return view('admin.customer.view_customer_payment_details', compact('cus_payments'));
    }

    public function deleteCustomerPayment($tran_id)
    {
        DB::beginTransaction();
        try {
            $c_payment = CustomerPayment::where('transaction_id', $tran_id)->first();

            if ($c_payment->payment_mode == "Bank") {
                BankStatement::where('transaction_id', $tran_id)->delete();
            }

            if ($c_payment->payment_mode == "Cash") {
                CashStatement::where('transaction_id', $tran_id)->delete();
            }

            CustomerStatement::where('transaction_id', $tran_id)->delete();

            //delete files
            $file_text = $c_payment->file;
            if ($file_text != "") {
                $files = explode(",", $file_text);
                foreach ($files as $file) {
                    if (is_dir(public_path())) {
                        unlink(public_path('img/files/income_files/customer_payment/' . $file));
                    } else {
                        unlink(base_path('img/files/income_files/customer_payment/' . $file));
                    }
                }
            }

            //finally delete from customer payment
            $status = $c_payment->delete();

            if ($status) {
                DB::commit();
                Session::flash('message', "Data Deleted Successfully!");
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollBack();
                Session::flash('message', "Data saving failed!");
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


    // public function viewCustomerStatement(Request $request)
    // {
    //     $user = Auth::user();
    //     $branchId = $request->branchId ?? $user->branchId;

    //     if ($request->date_range) {
    //         $date_range = date_range_to_arr($request->date_range);
    //     } elseif ($request->date_range == '' && $request->search_text == '') {
    //         $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
    //     }

    //     $statements = new CustomerStatement();

    //     if ($branchId == 'head_office') {
    //         $statements = $statements->where('branchId', null);
    //     } elseif ($branchId != '') {
    //         $statements = $statements->where('branchId', $branchId);
    //     }

    //     if (isset($date_range)) {
    //         $statements = $statements->whereBetween('posting_date', $date_range);
    //     }

    //     if ($request->search_text != '') {
    //         $statements = $statements->where(function ($query) use ($request) {
    //             $query->where('transaction_id', 'LIKE', '%' . $request->search_text . '%')
    //                 ->orWhere('description', 'LIKE', '%' . $request->search_text . '%')
    //                 ->orWhere('debit', 'LIKE', '%' . $request->search_text . '%')
    //                 ->orWhere('credit', 'LIKE', '%' . $request->search_text . '%')
    //                 ->orWhere('balance', 'LIKE', '%' . $request->search_text . '%')
    //                 ->orWhereHas('customer', function ($q) use ($request) {
    //                     $q->where('name', 'LIKE', '%' . $request->search_text . '%');
    //                 });
    //         });
    //     }

    //     // Calculate balance: sum(credit - debit)
    //     $bal_f = $statements->sum(DB::raw('credit - debit'));

    //     // Order by posting_date descending, then by id descending
    //     $statements = $statements->orderBy('posting_date', 'DESC')->orderBy('id', 'DESC')->get();

    //     // Fetch customers matching search text
    //     $customer = Customer::where('name', 'LIKE', '%' . $request->search_text . '%')->get();

    //     $branches = Branch::orderBy('name')->get();

    //     return view('admin.customer.view_customer_statement', compact('statements', 'bal_f', 'customer', 'branches'));
    // }

public function viewCustomerStatement(Request $request)
{
    $user = Auth::user();
    $branchId = $request->branchId ?? $user->branchId;

    // ALWAYS set default date range first (last 30 days)
    $start_date = date('Y-m-d', strtotime('-30 days'));
    $end_date = date('Y-m-d');
    $date_range = [$start_date, $end_date];
    
    // Override with user's date range if provided
    if ($request->filled('date_range')) {
        $dates = explode(' - ', $request->date_range);
        
        if (count($dates) === 2) {
            try {
                // Parse dates (handles MM/DD/YYYY format from date picker)
                $start_date = date('Y-m-d', strtotime(trim($dates[0])));
                $end_date = date('Y-m-d', strtotime(trim($dates[1])));
                
                $date_range = [$start_date, $end_date];
            } catch (\Exception $e) {
                // If parsing fails, keep default date range
                Log::warning('Invalid date range format in customer statement', [
                    'input' => $request->date_range,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    // Build query
    $statements = CustomerStatement::query();

    // Branch filter
    if ($branchId == 'head_office') {
        $statements = $statements->whereNull('branchId');
    } elseif ($branchId != '') {
        $statements = $statements->where('branchId', $branchId);
    }

    // Date range filter (always applied)
    $statements = $statements->whereBetween('posting_date', $date_range);

    // Search filter
    if ($request->filled('search_text')) {
        $search_term = '%' . trim($request->search_text) . '%';
        $statements = $statements->where(function ($query) use ($search_term) {
            $query->where('transaction_id', 'LIKE', $search_term)
                ->orWhere('description', 'LIKE', $search_term)
                ->orWhere('debit', 'LIKE', $search_term)
                ->orWhere('credit', 'LIKE', $search_term)
                ->orWhere('balance', 'LIKE', $search_term)
                ->orWhereHas('customer', function ($q) use ($search_term) {
                    $q->where('name', 'LIKE', $search_term);
                });
        });
    }

    // Calculate balance: sum(credit - debit)
    $bal_f = $statements->sum(DB::raw('credit - debit'));

    // Order by posting_date descending, then by id descending
    $statements = $statements->orderBy('posting_date', 'DESC')->orderBy('id', 'DESC')->get();

    // Fetch customers matching search text
    $customer = Customer::where('name', 'LIKE', '%' . $request->search_text . '%')->get();

    $branches = Branch::orderBy('name')->get();

    // Pass date range info to view for display
    $date_range_display = date('d-m-Y', strtotime($date_range[0])) . ' to ' . date('d-m-Y', strtotime($date_range[1]));

    return view('admin.customer.view_customer_statement', compact('statements', 'bal_f', 'customer', 'branches', 'date_range_display'));
}


public function monthlyReport(Request $request)
{

    if ($request->filled('from_date') && $request->filled('to_date')) {
        $date_range = [$request->from_date, $request->to_date];
    } else {
        $date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
    }

    $search_name = $request->search_name ?? '';



    $customerIdsFromChallans = ProductSale::whereBetween('sell_date', $date_range)
        ->pluck('customer_id')
        ->toArray();

    $customerIdsFromPayments = CustomerPayment::whereBetween('ref_date', $date_range)
        ->pluck('customer_id')
        ->toArray();

    $relevantCustomerIds = array_unique(array_merge($customerIdsFromChallans, $customerIdsFromPayments));

    $customersQuery = Customer::whereIn('id', $relevantCustomerIds);

    if ($search_name) {
        $customersQuery->where(function ($q) use ($search_name) {
            $q->where('name', 'LIKE', "%{$search_name}%")
                ->orWhere('email', 'LIKE', "%{$search_name}%")
                ->orWhere('phone', 'LIKE', "%{$search_name}%")
                ->orWhere('extra_phone_no', 'LIKE', "%{$search_name}%");
        });
    }

    $customers = $customersQuery->orderBy('name', 'ASC')->get();


    $customerReports = [];
    $startDate = $date_range[0];


    foreach ($customers as $customer) {

        

        $challans = $customer->challans()
            ->with('mix_design')
            ->whereBetween('sell_date', $date_range)
            ->get();

        $qtyCum = 0;
        $totalAmount = 0;

        foreach ($challans as $challan) {
            $qtyCum += $challan->cuM;

            $rate = $challan->rate > 0
                ? $challan->rate
                : ($challan->mix_design ? $challan->mix_design->rate : 0);

            $cft = $challan->cuM * 35.315;
            $totalAmount += $cft * $rate;
        }

        $qtyCft = $qtyCum * 35.315;


        $collectionAmount = CustomerPayment::where('customer_id', $customer->id)
            ->whereBetween('ref_date', $date_range)
            ->sum('paid_amount');



        $balance = $totalAmount - $collectionAmount;
        $balanceAbs = abs($balance);
        $balanceLabel = $balance < 0 ? 'Advance' : 'Due';



        $prevChallans = $customer->challans()
            ->with('mix_design')
            ->where('sell_date', '<', $startDate)
            ->get();

        $prevSaleAmount = 0;

        foreach ($prevChallans as $challan) {
            $rate = $challan->rate > 0
                ? $challan->rate
                : ($challan->mix_design ? $challan->mix_design->rate : 0);

            $cft = $challan->cuM * 35.315;
            $prevSaleAmount += $cft * $rate;
        }

        $prevPaidAmount = CustomerPayment::where('customer_id', $customer->id)
            ->where('ref_date', '<', $startDate)
            ->sum('paid_amount');

        $previousBalance = $prevSaleAmount - $prevPaidAmount;
        $previousBalanceAbs = abs($previousBalance);
        $previousBalanceLabel = $previousBalance < 0 ? 'Advance' : 'Due';

        // $monthlyEnding =  $previousBalance - $balance ;
        //  $monthlyEnding = 0 ;F

        if ($previousBalance < 0 && $balance > 0) {
            // Previous Advance, Current Due
            $monthlyEnding = $balance - abs($previousBalance);
        } elseif ($previousBalance > 0 && $balance < 0) {
            // Previous Due, Current Advance
            $monthlyEnding = $previousBalance - abs($balance);
        } else {
            // Both same type or zero
            $monthlyEnding = $previousBalance + $balance;
        }


        if ($monthlyEnding < 0) {
            $monthlyEndingLabel = 'Advance';
        } else {
            $monthlyEndingLabel = 'Due';
        }

        $monthlyEndingAbs = abs($monthlyEnding);
        $customerReports[] = [
            's_no' => count($customerReports) + 1,
             'customer_id' => $customer->id,
            'customer_name' => $customer->name,

            'qty_cum' => number_format($qtyCum, 2),
            'qty_cft' => number_format($qtyCft, 3),

            'total_amount' => number_format($totalAmount, 2),
            'collection_amount' => number_format($collectionAmount, 2),

            'balance_amount' => number_format($balanceAbs, 2),
            'balance_label' => $balanceLabel,

            'previous_balance_amount' => number_format($previousBalanceAbs, 2),
            'previous_balance_label' => $previousBalanceLabel,

            'monthly_ending_amount' => number_format($monthlyEndingAbs, 2),
            'monthly_ending_label' => $monthlyEndingLabel,

           
            'Outstanding_balance'  =>$prevPaidAmount,
        ];
    }


    $date_info = "Report from {$date_range[0]} to {$date_range[1]}";

    return view('admin.customer.monthly_customer_report', compact('customerReports', 'date_info'));
}





}
