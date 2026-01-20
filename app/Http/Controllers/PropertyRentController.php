<?php

namespace App\Http\Controllers;

use App\Models\BankInfo;
use App\Models\BankStatement;
use App\Models\Branch;
use App\Models\CashStatement;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Location;
use App\Models\OwnerPayment;
use App\Models\OwnerPaymentLog;
use App\Models\OwnerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PropertyRentController extends Controller
{
    public function createOwner()
    {
        $owners = OwnerProfile::where('type', 'Land Owner')->get();
        return view('admin.property-rent.add_land_owner', ['owners' => $owners]);
    }

    public function saveOwner(Request $request)
    {
        $rules = [
            'type' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'location_name' => 'required',
        ];

        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            $user_data = Auth::user();
            //insert into owner_profile
            $profile = new OwnerProfile();
            $profile->name = $request->name;
            $profile->phone = $request->phone;
            $profile->type = $request->type;
            $profile->email = $request->email;
            $profile->status = '1';
            $profile->user_id = $user_data->id;
            $status = $profile->save();

            //TODO:: Retouch Location?
            //insert into location
            $location = new Location();
            $location->name = $request->location_name;
            $location->location_details = $request->location_details;
            $location->profile_id = $profile->id;
            $location->user_id = $user_data->id;
            $location->save();

            if ($status) {
                DB::commit();
                Session::flash('message', 'Data Saved Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollBack();
                Session::flash('message', 'Data saving failed');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }
    }

    public function updateOwner(Request $request)
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];

        $this->validate($request, $rules);

        $row = OwnerProfile::find($request->id);
        $row->name = $request->name;
        $row->phone = $request->phone;
        $row->email = $request->email;
        $status = $row->save();
        if ($status) {
            Session::flash('message', 'Data Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data saving failed');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function deleteOwner($id)
    {
        $row = OwnerProfile::find($id);
        $status = $row->delete();
        if ($status) {
            Session::flash('message', 'Data Deleted Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data saving failed');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function saveLocation(Request $request)
    {
        $rules = [
            'profile_id' => 'required',
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $location = new Location();
        $location->name = $request->name;
        $location->profile_id = $request->profile_id;
        $location->location_details = $request->details;
        $location->user_id = $user_data->id;
        $status = $location->save();

        if ($status) {
            Session::flash('message', 'Data Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data saving failed');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

    }

//    public function editLocation($id)
//    {
//        $location = Location::find($id);
//        return view('admin.property-rent.edit_location',compact('location'));
//    }

    public function updateLocation(Request $request)
    {
        $rules = [
            'location_id' => 'required|numeric',
            'location_name' => 'required',
        ];
        $this->validate($request, $rules);

        $location = Location::findOrFail($request->location_id);
        $location->name = $request->location_name;
        $location->location_details = $request->location_details;
        $status = $location->save();

        if ($status) {
            Session::flash('message', 'Data Updated Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        } else {
            Session::flash('message', 'Data saving failed');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }


    }

    public function viewLandHouseOwner(Request $request)
    {
        $owners = new OwnerProfile();
        if ($request->type != "") {
            $owners = $owners->where('type', $request->type);
        }
        if ($request->search_text != "") {
            $owners = $owners->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->search_text . '%');
            });
        }
        $owners = $owners->orderBy('id', 'DESC')->get();

        // return view('admin.property-rent.view_land_house_owner', compact('owners'));

         $grandta = 0;
    $totalDue = 0;

    foreach ($owners as $owner) {
        $owner_payable = $owner->rentInfos->sum('payable_amount') - $owner->paidAmount();

        if ($owner_payable > 0) {
            $grandta += $owner_payable;
        } else {
            $totalDue += abs($owner_payable);
        }
    }

    return view('admin.property-rent.view_land_house_owner', compact('owners', 'grandta', 'totalDue'));
    }

    public function viewRentPayment(Request $request, $owner_id)
    {
        if ($request->date_range) {
            $date_range = date_range_to_arr($request->date_range);
        }
//        elseif($request->date_range == '' && $request->search_text == ''){
//            $date_range = [date('Y-m-d',strtotime('-30 days')), date('Y-m-d')];
//        }

        $rent_payments = OwnerPaymentLog::where('profile_id', $owner_id);

        if (isset($date_range)) {
            $rent_payments = $rent_payments->whereBetween('payment_date', [
                Carbon::parse($date_range[0])->format('Y-m-d'),
                Carbon::parse($date_range[1])->format('Y-m-d')
            ]);
        }

        if ($request->search_text) {
            $rent_payments = $rent_payments->where(function ($query) use ($request) {
                $query->where('transaction_id', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('rent_type', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('month', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('payment_mode', 'LIKE', $request->search_text . '%')
                    ->orWhere('paid_amount', $request->input('search_name'))
                    ->orWhereHas('location', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->search_text . '%');
                    });
            });
        }
        $rent_payments = $rent_payments->orderBy('id', 'DESC')->get();

        $owner_info = OwnerProfile::find($owner_id);
        return view('admin.property-rent.view_rent_payment', compact('rent_payments', 'owner_info'));
    }

    public function createRentInfo()
    {
        $owners = OwnerProfile::where('type', '=', 'Land Owner')->orderBy('name')->get();
        return view('admin.property-rent.add_rent_info', compact('owners'));
    }

    public function saveRentInfo(Request $request)
    {
        $rules = [
            'profile_id' => 'required|numeric',
            'location_id' => 'required|unique:owner_payments',
            'total_month' => 'required|numeric',
            'monthly_rent' => 'required|numeric',
            'payable_amount' => 'required|numeric',
        ];

        $this->validate($request, $rules);
        $user_data = Auth::user();

        //insert into owner_payments table
        $rent_info = new OwnerPayment();
        $rent_info->location_id = $request->location_id;
        $rent_info->profile_id = $request->profile_id;
        $rent_info->total_month = $request->total_month;
        $rent_info->monthly_rent = $request->monthly_rent;
        $rent_info->description = $request->description;
        $rent_info->payable_amount = $request->payable_amount;
        $rent_info->due_amount = $request->payable_amount;
        $rent_info->status = '1';
        $rent_info->user_id = $user_data->id;
        $status = $rent_info->save();

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

    public function updateLandRentInfo(Request $request)
    {
        $rules = [
            'due_amount' => 'required',
            'renewal_amount' => 'required',
            'l_id' => 'required',
            'type_of_owner' => 'required',
            'prof_id' => 'required'
        ];
        $this->validate($request, $rules);

        //update land rent payment in owner payments table
        if ($request->type_of_owner == "Land Owner") {
            $rent_infos = OwnerPayment::where('location_id', $request->l_id)->first();
            $rent_infos->payable_amount += $request->renewal_amount;
            $rent_infos->due_amount += $request->renewal_amount;

            try {
                $rent_infos->save();

                Session::flash('message', 'Data Saved Successfully!');
                Session::flash('m-class', 'alert-success');
            } catch (\Exception $ex) {
                Session::flash('message', $ex->getMessage());
                Session::flash('m-class', 'alert-danger');
            }
        }
        return redirect()->back();

    }


    public function payRent($owner_id)
    {
        $owner = OwnerProfile::where('id', $owner_id)->first();
        $owner_locations = Location::where('profile_id', $owner->id)->get();
        $banks = BankInfo::orderBy('bank_name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.property-rent.pay_rent', compact('owner', 'owner_locations', 'banks', 'branches'));
    }

    public function saveRentPayment(Request $request)
    {
        $rules = [
            'profile_id' => 'required|numeric',
            'location_id' => 'required|numeric',
            'rent_type' => 'required',
            'payment_date' => 'required',
            'month' => 'required',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,==,Bank',
            'paid_amount' => 'required|numeric',
            'branchId' => 'nullable|numeric'
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $payment_date = date('Y-m-d', strtotime($request->payment_date));
        $cheque_date = date('Y-m-d', strtotime($request->cheque_date));
        DB::beginTransaction();
        try {
            $payment_id = OwnerPaymentLog::max('id');
            if ($payment_id == "") $payment_id = 1; else $payment_id++;
            if ($request->type == "Land Owner") $tran_id = 'LRP-' . $payment_id; else $tran_id = 'HRP-' . $payment_id;

            $rent_payment = new OwnerPaymentLog();
            $rent_payment->transaction_id = $tran_id;
            $rent_payment->location_id = $request->location_id;
            $rent_payment->rent_type = $request->rent_type;
            $rent_payment->month = $request->month;
            $rent_payment->profile_id = $request->profile_id;
            $rent_payment->payment_date = $payment_date;
            $rent_payment->description = $request->description;
            $rent_payment->payment_mode = $request->payment_mode;
            $rent_payment->paid_amount = $request->paid_amount;
            $rent_payment->user_id = $user_data->id;
            $rent_payment->branchId = $request->branchId;
            $status = $rent_payment->save();

            //update land rent payment in owner payments table
            if ($request->type == "Land Owner") {
                $rent_infos = OwnerPayment::where('location_id', $request->location_id)->first();
                $rent_infos->paid_amount += $request->paid_amount;
                $rent_infos->due_amount = $rent_infos->due_amount - $request->paid_amount;
                $rent_infos->save();
            }

            $ex_type_id = ExpenseType::where('type_name', 'Rent Payment')->value('id');
            //save in expense table
            $expense = new Expense();
            $expense->transaction_id = $tran_id;
            $expense->expense_name = 'Rent Payment to ' . $request->type;
            $expense->description = 'Rent Payment - ' . $request->type . ', Month - ' . $request->month . ', ' . $request->description;
            $expense->expense_type_id = $ex_type_id;
            $expense->date = $payment_date;
            $expense->table_name = 'owner_payment_logs';
            $expense->amount = $request->paid_amount;
            $expense->payment_mode = $request->payment_mode;
            $expense->user_id = $user_data->id;
            $expense->branchId = $request->branchId;
            $expense->save();

            if ($request->payment_mode == 'Bank') {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                //CHECKING BANK BALANCE
                if ($request->paid_amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank!');
                }

                //save data in bank_statements
                $b_statement = new BankStatement();
                $b_statement->transaction_id = $tran_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Rent Payment - ' . $request->type . ', Month - ' . $request->month . ', ' . $request->description;
                $b_statement->table_name = 'owner_payment_logs';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->debit = $request->paid_amount;
                $b_statement->balance = $bank_bal - $request->paid_amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $request->branchId;
                $b_statement->user_id = $user_data->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash') {

                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->paid_amount > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                $c_statement = new CashStatement();
                $c_statement->transaction_id = $tran_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Rent Payment - ' . $request->type . ', Month - ' . $request->month . ', ' . $request->description;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->table_name = 'owner_payment_logs';
                $c_statement->debit = $request->paid_amount;
                $c_statement->balance = $cash_bal - $request->paid_amount;
                $c_statement->branchId = $request->branchId;
                $c_statement->user_id = $user_data->id;
                $c_statement->save();

            }
            if ($status) {
                DB::commit();
                Session::flash('message', 'Pay rent saved Successfully!');
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

    public function deleteRentPayment($tran_id)
    {
        $row = OwnerPaymentLog::where('transaction_id', $tran_id)->first();

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


            //update owner info table
            $owner_type = OwnerProfile::where('id', $row->profile_id)->first();
            if ($owner_type == 'Land Owner') {
                $payment_info_row = OwnerPayment::where('location_id', $row->location_id)
                    ->where('profile_id', $row->profile_id)
                    ->first();
                $paid_amt = $payment_info_row->paid_amount - $row->paid_amount;
                $due_amt = $payment_info_row->due_amount + $row->paid_amount;

                $payment_info_row->update(array('paid_amount' => $paid_amt, 'due_amount' => $due_amt));
            }

            ///finally delete ownerPaymentLogs
            $status = $row->delete();


            if ($status) {
                DB::commit();
                Session::flash('message', 'Deleted Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            } else {
                DB::rollback();
                Session::flash('message', 'Deleting failed!');
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


    public function loadRentInfo(Request $request)
    {
        $location_id = $request->location_id;
        $payment_logs = OwnerPaymentLog::where('location_id', $location_id)->orderBy('id', 'desc')->first();
        $rent_info = OwnerPayment::where('location_id', $location_id)->first();
        $month_name = "";
        if ($payment_logs) {
            $month_name = $payment_logs->month;
        }

        if ($request->owner_type == "Land Owner") {
            if (!$rent_info) {
                $response = array(
                    'success' => 0,
                    'month' => $month_name,
                );
            } else {
                $response = array(
                    'success' => 1,
                    'month' => $month_name,
                    'due' => $rent_info->due_amount,
                );
            }
        } else {
            $response = array(
                'success' => 1,
                'month' => $month_name,
            );
        }

        return response()->json($response);
    }

    public function loadLocation(Request $request)
    {
        $id = $request->profile_id;
        $owner_locations = Location::where('profile_id', $id)->get();

        $response = array(
            'locations' => $owner_locations,
        );
        return response()->json($response);
    }

    public function loadOwner(Request $request)
    {
        $type = $request->type;
        $owner = OwnerProfile::where('type', $type)->get();

        $response = array(
            'owners' => $owner,
        );
        return response()->json($response);
    }

}



