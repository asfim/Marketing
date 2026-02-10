<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\BankStatement;
use App\Models\CashStatement;
use App\Models\BankInfo;
use App\Models\Branch;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssetInstallment;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if($request->date_range){
            $date_range = date_range_to_arr($request->date_range);
        } elseif($request->date_range == '' && $request->search_text == ''){
            $date_range = [date('Y-m-d',strtotime('-1 years')), date('Y-m-d')];
        }

        $assets  = new Asset();

        if(isset($date_range)) {
            $assets = $assets->whereBetween('purchase_date', $date_range);
        }

        if($request->search_text) {
            $assets = $assets->where(function($query) use ($request) {
                $query->where('asset_id', $request->search_text)
                    ->orWhere('name', 'LIKE', '%'.$request->search_text.'%')
                    ->orWhere('purchase_amount', $request->search_text)
                    ->orWhere('salvage_value', $request->search_text)
                    ->orWhere('asset_life_year', $request->search_text)
                    ->orWhere('depreciated_amount', $request->search_text)
                    ->orWhereHas('asset_type', function($q) use ($request) {
                        $q->where('name', 'LIKE','%'.$request->search_text.'%');
                    });
            });
        }

        if($user->branchId != '') {
            $assets = $assets->where('branchId',$user->branchId);
        }

        $assets = $assets->orderBy('id','desc')->get();

        $banks = BankInfo::all();
        $asset_types = AssetType::orderBy('name','ASC')->get();
        return view('admin.asset.view_asset',compact('assets','asset_types','banks'));
    }

    public function create()
    {
        $banks = BankInfo::orderBy('bank_name','ASC')->get();
        $asset_types = AssetType::orderBy('name','ASC')->get();
        $branches = Branch::all();
        return view('admin.asset.add_asset',compact('banks','asset_types','branches'));
    }

    public function store(Request $request)
    {
        $rules = [
            'asset_type_id' => 'required|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'bail|required_if:payment_mode,Bank',
            'name' => 'required',
            'installment_status' => 'required',
            'monthly_amount' => 'required_if:installment_status,1',
            'total_installment_amount' => 'required_if:installment_status,1',
            'purchase_date' => 'required',
            'purchase_amount' => 'required|numeric|min:1',
            'asset_life_year' => 'required|numeric',
            'salvage_value' => 'required|numeric|min:1',
        ];

        $this->validate($request, $rules);
        $user_data  = Auth::user();
        $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
        $cheque_date = date('Y-m-d',strtotime($request->cheque_date));

        DB::beginTransaction();
        try {
            //ASSIGN MAX ASSET ID
            $ex_id = Asset::max('id');
            if ($ex_id == "") $ex_id = 1;
            else $ex_id++;

            //CALCULATE ASSET DEPRECIATION
            $depreciation_per_year = ($request->purchase_amount - $request->salvage_value) / $request->asset_life_year;

            $asset = new Asset();
            $asset->transaction_id = 'ASSET-' . $ex_id;
            $asset->name = $request->name;
            $asset->description = $request->description;
            $asset->purchase_date = $purchase_date;
            $asset->purchase_amount = $request->purchase_amount;
            $asset->salvage_value = $request->salvage_value;
            $asset->asset_life_year = $request->asset_life_year;
            $asset->asset_id = $request->asset_id;
            $asset->asset_type_id = $request->asset_type_id;
            $asset->installment_status = $request->installment_status;
            $asset->installment_number = $request->installment_number;
            $asset->installment_paid = $request->installment_paid;
            $asset->monthly_amount = $request->monthly_amount;
            $asset->total_installment_amount = $request->total_installment_amount;
            $asset->payment_mode = $request->payment_mode;
            $asset->depreciation = $depreciation_per_year;
            $asset->user_id = $user_data->id;
            $asset->branchId = $request->branchId;
            $status = $asset->save();
            $new_id = $asset->id;

            //save in asset_installment table if installment status yes
            if ($request->installment_status == '1') {
                $ex_id = AssetInstallment::max('id');
                if ($ex_id == "") $ex_id = 1;
                else $ex_id++;

                $asset_ins = new AssetInstallment();
                $asset_ins->transaction_id = 'ASINS-' . $ex_id;
                $asset_ins->asset_id = $new_id;
                $asset_ins->name = $request->name;
                $asset_ins->description = $request->description;
                $asset_ins->date = $purchase_date;
                $asset_ins->installment_amount = $request->purchase_amount;
                $asset_ins->installment_paid = $request->installment_paid;
                $asset_ins->payment_mode = $request->payment_mode;
                $asset_ins->user_id = $user_data->id;
                $asset_ins->branchId = $request->branchId;
                $asset_ins->save();
            }

            //save in cash or bank statement
            if ($request->payment_mode == 'Bank' && $request->purchase_amount>0) {
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                if ($request->purchase_amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank');
                }

                //save data in bank_statements
                $b_statement = new BankStatement();
                $b_statement->transaction_id = ($request->installment_status == 1)?'ASINS-'.$ex_id:'ASSET-'.$ex_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Asset Purchase - ' . $request->name;
                $b_statement->table_name = 'assets';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->debit = $request->purchase_amount;
                $b_statement->balance = $bank_bal - $request->purchase_amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->user_id = $user_data->id;
                $b_statement->save();
            }

            if ($request->payment_mode == 'Cash' && $request->purchase_amount>0) {
                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->purchase_amount > $cash_bal) {
                    throw new \Exception('Cash Balance is not sufficient for expense');
                }

                //save data in cash_statements
                $c_statement = new CashStatement();
                $c_statement->transaction_id = ($request->installment_status == 1)?'ASINS-'.$ex_id:'ASSET-'.$ex_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Asset Purchase - ' . $request->name;
                $c_statement->table_name = 'assets';
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->debit = $request->purchase_amount;
                $c_statement->balance = $cash_bal-$request->purchase_amount;
                $c_statement->user_id = $user_data->id;
                $c_statement->save();
            }

            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            Session::flash('message', $e->getMessage());
            Session::flash('m-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

        if ($status) {
            Session::flash('message', 'Added Successfully');
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
            'asset_type_id' => 'required|numeric',
            'name' => 'required',
            'purchase_date' => 'required',
            'purchase_amount' => 'required|numeric',
            'asset_life_year' => 'required|numeric',
            'salvage_value' => 'required|numeric',
//            'installment_status' => 'required'
        ];
        $this->validate($request, $rules);
        $purchase_date = date('Y-m-d',strtotime($request->purchase_date));
        DB::beginTransaction();
        try{
            $user_data  = Auth::user();
            $depreciation_per_year = ($request->purchase_amount - $request->salvage_value)/$request->asset_life_year;

            $asset = Asset::find($request->id);
            $asset->name = $request->name;
            $asset->description = $request->description;
            $asset->purchase_date = $purchase_date;
            $asset->purchase_amount = $request->purchase_amount;
//            $asset->installment_status = $request->installment_status;
            $asset->salvage_value = $request->salvage_value;
            $asset->asset_life_year = $request->asset_life_year;
            $asset->asset_id = $request->asset_id;
            $asset->asset_type_id = $request->asset_type_id;
            $asset->depreciation = $depreciation_per_year;
            $asset->user_id = $user_data->id;
            $status  = $asset->save();


            if ($asset->payment_mode == 'Bank') {
                $b_statement = BankStatement::where('transaction_id', $asset->transaction_id)->first();
                $b_statement->description = 'Asset Purchase - ' . $request->name;
                $b_statement->debit = $request->purchase_amount;
                $b_statement->save();
            }

            if ($asset->payment_mode == 'Cash') {
                $c_statement = CashStatement::where('transaction_id', $asset->transaction_id)->first();
                $c_statement->description = 'Asset Purchase - ' . $request->name;
                $c_statement->debit = $request->purchase_amount;
                $c_statement->save();
            }

            DB::commit();
        }catch (\Exception $ex) {
            DB::rollBack();
            Session::flash('message',$ex->getMessage());
            return redirect()->back();
        }

        if($status) {
            Session::flash('message','Updated Successfully!');
            Session::flash('m-class','alert-success');
            return redirect()->back();
        } else {
            Session::flash('message','Updating Data failed!');
            Session::flash('m-class','alert-danger');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $asset = Asset::find($id);
            //delete from bank
            if ($asset->payment_mode == 'Bank') {
                BankStatement::where('transaction_id', $asset->transaction_id)->delete();
            }
            if ($asset->payment_mode == 'Cash') {
                CashStatement::where('transaction_id', $asset->transaction_id)->delete();
            }
            if ($asset->installment_status == 1) {
                $installments = AssetInstallment::where('asset_id', $asset->id)->get();
                foreach ($installments as $installment) {
                    if ($installment->payment_mode == 'Bank') {
                        BankStatement::where('transaction_id', $installment->transaction_id)->delete();
                    }

                    if ($installment->payment_mode == 'Cash') {
                        CashStatement::where('transaction_id', $installment->transaction_id)->delete();
                    }
                }

                AssetInstallment::where('asset_id', $asset->id)->delete();
            }

            $status = $asset->delete();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            Session::flash('message', $ex->getMessage());
            Session::flash('m-class','alert-danger');
            return redirect()->back();
        }

        if ($status) {
            Session::flash('message','Deleted Successfully!');
            Session::flash('m-class','alert-success');
            return redirect()->back();
        }
    }

    public function assetInstallmentCreate($id)
    {
        $asset = Asset::find($id);
        $branches = Branch::all();
        $banks = BankInfo::orderBy('bank_name','ASC')->get();

        return view('admin.asset.add_asset_installment',compact('asset','banks','branches'));
    }

    public function saveAssetInstallment(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'payment_mode' => 'required',
            'bank_id' => 'required_if:payment_mode,Bank',
            'name' => 'required',
            'date' => 'required',
            'installment_amount' => 'required|numeric',
            'branchId' => 'nullable|numeric'
        ];

        $this->validate($request, $rules);

        $user_data = Auth::user();
        $id = $request->id;
        $date = date('Y-m-d',strtotime($request->date));
        $cheque_date = date('Y-m-d',strtotime($request->cheque_date));

        DB::beginTransaction();
try {
    //asset info for description
    $asset_row = Asset::where('id', $id)->first();
    if ($asset_row->purchase_amount+$request->installment_amount > $asset_row->total_installment_amount) {
        throw new \Exception( 'Payment amount is greater than purchase amount!');
    }
    $ex_id = AssetInstallment::max('id');
    if ($ex_id == "") $ex_id = 1;
    else $ex_id++;


    $payment_mode_value = $request->payment_mode;
    if ($request->payment_mode == 'Bank' && $request->filled('bank_id')) {
        $bank_info = BankInfo::find($request->bank_id);


        if ($bank_info) {
            $payment_mode_value = $bank_info->bank_name; 
            
        }
    }

    $asset_installment_ = new AssetInstallment();
    $asset_installment_->transaction_id = 'ASINS-' . $ex_id;
    $asset_installment_->asset_id = $id;
    $asset_installment_->name = $request->name;
    $asset_installment_->description = $request->description;
    $asset_installment_->date = $date;
    $asset_installment_->installment_amount = $request->installment_amount;
    $asset_installment_->payment_mode = $payment_mode_value;
    $asset_installment_->user_id = $user_data->id;
    $asset_installment_->branchId = $request->branchId;
    $status = $asset_installment_->save();

            //save in cash or bank statement
            if ($request->payment_mode == 'Bank') {
                //save data in bank_statements
                $bank_info = BankInfo::find($request->bank_id);
                $bank_bal = $bank_info->balance();
                if ($request->installment_amount > $bank_bal) {
                    throw new \Exception('Insufficient Balance in bank');
                }

                $b_statement = new BankStatement();
                $b_statement->transaction_id = 'ASINS-' . $ex_id;
                $b_statement->posting_date = date('Y-m-d');
                $b_statement->description = 'Installment for ' . $asset_row->name . 'Id: ' . $asset_row->asset_id . ', ' . $request->get('description');
                $b_statement->table_name = 'asset_installments';
                $b_statement->cheque_no = $request->cheque_no;
                $b_statement->ref_date = $cheque_date;
                $b_statement->debit = $request->installment_amount;
                $b_statement->balance = $bank_bal - $request->installment_amount;
                $b_statement->bank_info_id = $request->bank_id;
                $b_statement->branchId = $request->branchId;
                $b_statement->user_id = $user_data->id;
                $b_statement->save();

            }

            if ($request->payment_mode == 'Cash') {
                //CHECK CASH BALANCE
                $cash_bal = cashBalance($request->branchId);
                if ($request->installment_amount > $cash_bal) {
                    throw new \Exception('Insufficient Balance in Cash!');
                }

                //save data in cash_statements
                $c_statement = new CashStatement();
                $c_statement->transaction_id = 'ASINS-' . $ex_id;
                $c_statement->posting_date = date('Y-m-d');
                $c_statement->description = 'Installment for ' . $asset_row->name . 'Id: ' . $asset_row->asset_id . ', ' . $request->get('description');
                $c_statement->table_name = 'asset_installments';
                $c_statement->debit = $request->installment_amount;
                $c_statement->receipt_no = $request->cheque_no;
                $c_statement->ref_date = $cheque_date;
                $c_statement->balance =  $cash_bal - $request->installment_amount;;
                $c_statement->branchId = $request->branchId;
                $c_statement->user_id = $user_data->id;
                $c_statement->save();
            }

            //update assets purchase amount table
            $update_asset_value = $asset_row->purchase_amount + $request->installment_amount;
            $status_two = $asset_row->update(['purchase_amount' => $update_asset_value]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            Session::flash('message', $ex->getMessage());
            Session::flash('m-class','alert-danger');
            return redirect()->back()->withInput();
        }

        if($status && $status_two)
        {
            Session::flash('message','Installment Paid Successfully');
            Session::flash('m-class','alert-success');
            return redirect()->back();
        } else {
            Session::flash('message','Saving Data failed!');
            Session::flash('m-class','alert-danger');
            return redirect()->back();
        }
    }

    public function viewAssetInstallment( Request $request)
    {
        $assetInstallments = AssetInstallment::where('asset_id', $request->id);
        $total_amount = $assetInstallments->sum('installment_amount');
        $assetInstallments = $assetInstallments->get();
        return view('admin.asset.view_asset_installment',compact('assetInstallments', 'total_amount'));
    }

    public function assetType()
    {
        $asset_types = AssetType::all();
        return view('admin.asset.asset_type',compact('asset_types'));
    }

    public function saveAssetType(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:asset_types',
        ]);

        try{
            $asset_type = new AssetType();
            $asset_type->name= $request->name;
            $asset_type->description = $request->description;
            $asset_type->user_id = '1';
            $asset_type->save();

            Session::flash('message','Added Successfully');
            Session::flash('m-class','alert-success');
            return redirect()->back();
        }catch (\Exception $ex) {
            Session::flash('message',$ex->getMessage());
            Session::flash('m-class','alert-danger');
            return redirect()->back();
        }

    }

    public function updateAssetType(Request $request)
    {
        $this->validate($request, [
            'type_name' => 'required|unique:asset_types,id,'.$request->id,
        ]);

        try{
            $asset_type = AssetType::find($request->id);
            $asset_type->name= $request->type_name;
            $asset_type->description = $request->a_description;
            $asset_type->save();

            Session::flash('message','Updated Successfully');
            Session::flash('m-class','alert-success');
            return redirect()->back();
        } catch (\Exception $ex) {
            Session::flash('message','Updating Data failed');
            Session::flash('m-class','alert-danger');
            return redirect()->back();
        }

    }

    public function deleteAssetType($id)
    {
        $row = AssetType::find($id);
        try{
            $status = $row->delete();
            if($status) {
                Session::flash('message','Deleted Successfully');
                Session::flash('m-class','alert-success');
                return redirect()->back();
            }
        }catch (\Exception $ex) {
            Session::flash('message',$ex->getMessage());
            return redirect()->back();
        }
    }

}
