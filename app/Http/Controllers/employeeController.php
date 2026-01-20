<?php

namespace App\Http\Controllers;

use App\Models\BankInfo;
use App\Models\BankStatement;
use App\Models\CashStatement;
use App\Models\Employee;
use App\Models\EmployeeSallarySetting;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class employeeController extends Controller
{
    protected $_employeeObj,
        $_branchObj,
        $_empSalaryObj;

    public function __construct()
    {
        $this->_employeeObj     = new Employee();
        $this->_empSalaryObj    = new EmployeeSallarySetting();
    }

    public function viewForm()
    {
        return view('admin_panel.pages.add_employee');
    }
    public function view(Request $request)
    {
        if($request->input('search_name') != "") {
            $employees_data  = $this->_employeeObj->where('employeeName', '=', $request->input('search_name'))
                ->orWhere('employeeId', '=', $request->input('search_name'))
                ->orWhere('employeeEmail', '=', $request->input('search_name'))
                ->orWhere('employeePhone', '=', $request->input('search_name'))
                ->orWhere('nationalId', '=', $request->input('search_name'))
                ->orWhere('joiningDate', '=', $request->input('search_name'))
                ->orWhere('salary', '=', $request->input('search_name'))
                ->orderBy('id', 'ASC')->get();
        }else {
            $employees_data = $this->_employeeObj->orderBy('id', 'ASC')->get();
        }
        $employee_data_array    = array();
        $employee_data_array_two    = array();

        if(count($employees_data) > 0){
            $i=0;
            foreach($employees_data as $employee){
                $i++;
                $employee_data_array[]      = '<tr>
                                                    <td><input type="checkbox" name="checkbox"/></td>
                                                    <td>'.$employee->employeeName.'</td>
                                                    <td>'.$employee->employeeEmail.'</td>
                                                    <td>'.$employee->employeePhone.'</td>
                                                    <td>'.$employee->employeeAddress.'</td>
                                                    <td>'.$employee->nationalId.'</td>
                                                    <td>'.$employee->joiningDate.'</td>
                                                    <td>'.$employee->salary.'</td>
                                                    <td><img src="'.asset('assets/images/employees/'.$employee->photo).'" width="96"></td>
                                                    <td>
                                                        <a href="#eModal'.$i.'" role="button" class="glyphicon glyphicon-pencil" data-toggle="modal"></a>&nbsp&nbsp
                                                        <a href="'.route('employee.destroy', $employee->id).'"
                                                           onclick="event.preventDefault();
                                                                            document.getElementById(\'employee_delete_form_'.$i.'\').submit();"
                                                           class="glyphicon glyphicon-trash"></a>
                        
                                                        <form id="employee_delete_form_'.$i.'" action="'.route('employee.destroy', $employee->id).'" method="POST" style="display: none;">
                                                            '.csrf_field().'
                                                        </form>
                                                    </td>
                                               </tr>';
            }
        }
        return view('admin_panel.pages.view_employee', compact('employee_data_array', 'employees_data'));
    }

    public function save(Request $request)
    {
        $rules  = [
            'employeeId'        => 'required|unique:employees|max:11',
            'employeeName'      => 'required',
            'employeeEmail'     => 'required|unique:employees',
            'employeePhone'     => 'required',
            'employeeAddress'   => 'required',
            'joiningDate'       => 'required',
            'salary'            => 'required',

        ];

        $this->validate($request, $rules);
        $inputs         = $request->all();
        $inputs['joiningDate']  = date("Y-m-d", strtotime($request->input('joiningDate')));

        if($file = $request->file('photo'))
        {
            $file_name  = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();

            $file_name      = $inputs['employeeId'].".".$file_extension;
            $path           = public_path();
            if(is_dir($path)){
                $img_path       = public_path('assets/images/employees');
            }else{
                $img_path       = 'assets/images/employees';
            }

            $file->move($img_path, $file_name);
            $inputs['photo']    = $file_name;
        }

        $employee_save      = $this->_employeeObj->create($inputs);
        if($employee_save->save()){
            Session::flash('message', 'Data save Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('employee.view');
        }else{
            Session::flash('message', 'Data save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $rules  = [
            'employeeId'        => 'required|unique:employees,employeeId,'.$id.'|max:11',
            'employeeName'      => 'required',
            'employeeEmail'     => 'required|unique:employees,employeeEmail,'.$id,
            'employeePhone'     => 'required',
            'employeeAddress'   => 'required',
            'joiningDate'       => 'required',
            'salary'            => 'required',

        ];

        $this->validate($request, $rules);
        $employee_data     = $this->_employeeObj->findOrFail($id);

        $inputs         = $request->all();
        $inputs['joiningDate']  = date("Y-m-d", strtotime($request->input('joiningDate')));

        if($file = $request->file('photo'))
        {
            $file_name  = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();

            $file_name      = $employee_data->employeeId.".".$file_extension;
            $path           = public_path();
            if(is_dir($path)){
                $img_path       = public_path('assets/images/employees');
            }else{
                $img_path       = 'assets/images/employees';
            }

            $file->move($img_path, $fileName);
            $inputs['photo']    = $file_name;
        }

        $employee_update      = $employee_data->update($inputs);
        if($employee_update == true){
            Session::flash('message', 'Data Update Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('employee.view');
        }else{
            Session::flash('message', 'Data Update Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        $employee_data      = $this->_employeeObj->findOrFail($id);
        $path           = public_path();
        if(is_dir($path)){
            $img_path       = public_path('assets/images/employees/'.$employee_data->photo);
        }else{
            $img_path       = 'assets/images/employees/'.$employee_data->photo;
        }
        $employee_delete      = $employee_data->delete();
        if($employee_delete == true){
            @unlink($img_path);
            Session::flash('message', 'Data Update Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->route('employee.view');
        }else{
            Session::flash('message', 'Data Update Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }
    }


    public function employeeSalaryCreate()
    {
        $employee_data  = $this->_employeeObj->orderBy('employeeId', 'ASC')->get();
        $employee_data_array    = array();
        if(count($employee_data) > 0){
            $i = 0;
            foreach ($employee_data as $employee) {
                $i++;
                $employee_data_array[]  = '<option value="'.$employee->employeeId.'___'.$employee->salary.'">'.$employee->employeeName.'</option>';
            }
        }

        $bankObj    = new BankInfo();
        $bank_data  = $bankObj->orderBy('id', 'ASC')->get();
        $bank_data_array    = array();
        if(count($bank_data) > 0)
        {
            $i = 0;
            foreach ($bank_data as $bank)
            {
                $i++;
                $bank_data_array[]  = '<option value="'.$bank->id.'">'.$bank->bank_name.'</option>';
            }
        }

        return view('admin_panel.pages.employee_salary_setting', compact('employee_data_array', 'bank_data_array'));
    }

    public function employeeSalarySetting(Request $request)
    {
        $user_data  = Auth::user();
        if($request->payment_mode == "Bank"){
            $rules  = [
                'employeeId'    => 'required',
                'payment_mode'  => 'required',
                'sMonth'        => 'required',
                'paymentDate'   => 'required',
                'bank_id'       => 'required',
                'check_no'      => 'required',
                'cheque_date'   => 'required'
            ];
        }else {
            $rules = [
                'employeeId' => 'required',
                'payment_mode' => 'required',
                'sMonth' => 'required',
                'paymentDate' => 'required'
            ];
        }

        $message    = [
            'employeeId.required'   => 'Employee field can not be null'
        ];
        $this->validate($request, $rules, $message);

        $inputs = $request->all();
        $inputs['paymentDate']  = date("Y-m-d", strtotime($request->input('paymentDate')));

        $empss_id   = $this->_empSalaryObj->max('id');
        if($empss_id == ""){
            $empss_id   = 1;
        }else{
            $empss_id++;
        }

        $inputs['transaction_id']   = "ESS-".$empss_id;
        $inputs['user_id']          = $user_data->id;


        //save expense type as employee_salary_setting
        $expense_type = new ExpenseType();
        $ext_row = $expense_type->where('category', 'Salary Expenses')->first();
        if(!$ext_row)
        {
            $expense_type->type_name = 'Salary Payment Expense';
            $expense_type->description = 'N/A';
            $expense_type->category = 'Salary Expenses';
            $expense_type->user_id = $user_data->id;

            try {
                $expense_type->save();
            } catch (Exception $ex) {
                Session::put('message',$ex->getMessage());
                return redirect()->back();
            }
        }

        $expenseObj = new Expense();
        $expenseObj->transaction_id     = $inputs['transaction_id'];
        $expenseObj->expense_name       = 'Employee Salary Payment';
        $expenseObj->description        = 'Employee Salary Payment for Month - '.$request->sMonth.', '.$request->get('description');
        $type_row = $expense_type->where('category', 'Salary Expenses')->first();
        $expenseObj->expense_type_id    = $type_row->id;
        $expenseObj->date               = $inputs['paymentDate'];
        $expenseObj->table_name         = 'employee_salary_settings';
        $expenseObj->amount             = $request->totalSalary;
        $expenseObj->payment_mode       = $request->payment_mode;
        $expenseObj->user_id            = $user_data->id;

        try{
            $expenseObj->save();
        } catch (Exception $ex) {
            Session::put('message',$ex->getMessage());
            return redirect()->back();
        }

        //save in cash or bank statement
        if($request->payment_mode == 'Bank')
        {

            //save data in bank_statements
            $b_statements = new BankStatement();
            $b_statements->transaction_id   = $inputs['transaction_id'];
            $b_statements->posting_date     = date('Y-m-d');
            $b_statements->description      = 'Employee Salary Payment for Month - '.$request->sMonth;
            $b_statements->table_name       = 'employee_salary_settings';
            $b_statements->cheque_no        = $request->get('check_no');
            $b_statements->ref_date         = $request->get('cheque_date');
            $b_statements->debit            = $request->totalSalary;
            $pre_balance                    = $b_statements->orderBy('id', 'desc')->first();
            $current_bal                    = $pre_balance->balance - $request->totalSalary;
            $b_statements->balance          = $current_bal;
            $b_statements->bank_info_id     = $request->get('bank_id');

            try{
                $b_statements->save();
            } catch (Exception $ex) {
                Session::put('message',$ex->getMessage());
                return redirect()->back();
            }

        }

        if($request->payment_mode == 'Cash')
        {

            //save data in cash_statements
            $c_statements = new CashStatement();
            $c_statements->transaction_id   = $inputs['transaction_id'];
            $c_statements->posting_date     = date('Y-m-d');
            $c_statements->description      = 'Employee Salary Payment for Month - '.$request->sMonth;
            $c_statements->table_name       = 'employee_salary_settings';
            $c_statements->debit            = $request->totalSalary;
            $pre_balance                    = $c_statements->orderBy('id', 'desc')->first();
            $current_bal                    = $pre_balance->balance - $request->totalSalary;
            $c_statements->balance          = $current_bal;

            try{
                $c_statements->save();
            }
            catch (Exception $ex) {
                Session::put('message',$ex->getMessage());
                return redirect()->back();
            }
        }

        $status = $this->_empSalaryObj->create($inputs);
        if($status->save())
        {
            Session::flash('message', 'Data save Successfully!');
            Session::flash('m-class', 'alert-success');
            return redirect()->back();
        }else{
            Session::flash('message', 'Data save Failed!');
            Session::flash('m-class', 'alert-danger');
            return redirect()->back();
        }

    }

    public function employeeSalaryReport(Request $request)
    {

        $current_month  = date("m");
        if($request->input('month') != "" || ($request->input('from_date') != "" && $request->input('to_date') != "")){

            $from_date  = date("Y-m-d", strtotime($request->input('from_date')));
            $to_date    = date("Y-m-d", strtotime($request->input('to_date')));
            if($request->input('month') != "" && $request->input('from_date') != "" && $request->input('to_date') != ""){

                $emp_salary_report_data = $this->_empSalaryObj->where('sMonth', '=', $request->input('month'))->whereBetween('paymentDate', [$from_date, $to_date])->get();

            }elseif($request->input('month') == "" && $request->input('from_date') != "" && $request->input('to_date') != "") {

                $emp_salary_report_data = $this->_empSalaryObj->whereBetween('paymentDate', [$from_date, $to_date])->get();

            }elseif ($request->input('month') != "" && $request->input('from_date') == "" && $request->input('to_date') == ""){

                $emp_salary_report_data = $this->_empSalaryObj->where('sMonth', '=', $request->input('month'))->get();

            }
        }else {
            $emp_salary_report_data = $this->_empSalaryObj->whereMonth('paymentDate', $current_month)->get();
        }
        $emp_salary_report_array    = array();
        if(count($emp_salary_report_data) > 0) {
            $i = 0;
            /** Bank Statement */
            $bankObj    = new BankInfo();
            $bank_data  = $bankObj->orderBy('id', 'ASC')->get();
            foreach ($emp_salary_report_data as $emp_salary) {
                $i++;
                $emp_salary_report_array[]  = '<tr>
                                                <td><input type="checkbox" name="checkbox"/></td>
                                                <td>'.$emp_salary->transaction_id.'</td>
                                                <td>'.$emp_salary->employees->employeeName.'</td>
                                                <td>'.$emp_salary->salary.'</td>
                                                <td>'.$emp_salary->employeeBonus.'</td>
                                                <td>'.$emp_salary->totalSalary.'</td>
                                                <td>'.$emp_salary->payment_mode.'</td>
                                                <td>'.$emp_salary->sMonth.'</td>
                                                <td>'.$emp_salary->paymentDate.'</td>
                                                <td>
                                                    <a href="'.route('employee.salaryReportDelete', $emp_salary->id).'"
                                                       onclick="event.preventDefault();
                                                                    if(confirm(\'Are you sure delete this item\')) {
                                                                        document.getElementById(\'employee_delete_form_'.$i.'\').submit();
                                                                        }else{
                                                                            return false;
                                                                        }"
                                                       class="glyphicon glyphicon-trash"></a>
                    
                                                    <form id="employee_delete_form_'.$i.'" action="'.route('employee.salaryReportDelete', $emp_salary->id).'" method="POST" style="display: none;">
                                                        '.csrf_field().'
                                                    </form>
                                                </td>
                                               </tr>';
            }
        }
        return view('admin_panel.pages.employee_salary_report', compact('emp_salary_report_array', 'emp_salary_report_data', 'bank_data'));
    }



    public function employeeSalaryReportDelete($id)
    {
        $emp_salary_data    = $this->_empSalaryObj->find($id);
        if($emp_salary_data != "") {

            if($emp_salary_data->payment_mode == "Cash"){
                if($emp_salary_data->cash_statements){

                    $emp_transaction_id = $emp_salary_data->transaction_id;
                    $separate_id        = explode("-", $emp_transaction_id);

                    $cash_update        = [
                        'transaction_id'    => "DEL_".$emp_transaction_id,
                    ];

                    //save data in cash_statements
                    $c_statements = new CashStatement();
                    $c_statements->transaction_id   = "ADJ_".$emp_transaction_id;
                    $c_statements->posting_date     = date('Y-m-d');
                    $c_statements->description      = 'Adjustment Employee Salary Payment for Deleted '.$emp_salary_data->paymentDate.' date entry';
                    $c_statements->table_name       = 'employee_salary_settings';
                    $c_statements->credit           = $emp_salary_data->totalSalary;
                    $pre_balance                    = $c_statements->orderBy('id', 'desc')->first();
                    $current_bal                    = $pre_balance->balance + $emp_salary_data->totalSalary;
                    $c_statements->balance          = $current_bal;
                }

                try{
                    $emp_salary_data->cash_statements()->update($cash_update);
                    $c_statements->save();

                }catch(\Exception $e){
                    Session::put('message', $e->getMessage());
                    return redirect()->back();
                }
            }

            if($emp_salary_data->payment_mode == "Bank") {
                if($emp_salary_data->bank_statements){

                    $bank_update        = [
                        'transaction_id'        => "DEL_".$emp_salary_data->transaction_id
                    ];
                    //save data in bank_statements
                    $b_statements = new BankStatement();
                    $b_statements->transaction_id   = "ADJ_".$emp_salary_data->transaction_id;
                    $b_statements->posting_date     = date('Y-m-d');
                    $b_statements->description      = 'Adjustment Employee Salary Payment for Deleted '.$emp_salary_data->paymentDate.' date entry';
                    $b_statements->table_name       = 'employee_salary_settings';
                    $b_statements->cheque_no        = $emp_salary_data->bank_statements->check_no;
                    $b_statements->ref_date         = $emp_salary_data->bank_statements->cheque_date;
                    $b_statements->credit           = $emp_salary_data->totalSalary;
                    $pre_balance                    = $b_statements->orderBy('id', 'desc')->first();
                    $current_bal                    = $pre_balance->balance + $emp_salary_data->totalSalary;
                    $b_statements->balance          = $current_bal;
                    $b_statements->bank_info_id     = $emp_salary_data->bank_statements->bank_info_id;
                }

                try{
                    $emp_salary_data->bank_statements()->update($bank_update);
                    $b_statements->save();

                }catch(\Exception $e){
                    Session::put('message', $e->getMessage());
                    return redirect()->back();
                }
            }

            if($emp_salary_data->expenses){
                try{
                    $emp_salary_data->expenses()->delete();
                }catch(\Exception $e){
                    Session::put('message', $e->getMessage());
                    return redirect()->back();
                }
            }

            $status = $emp_salary_data->delete();;
            if($status == true)
            {
                Session::flash('message', 'Data Delete Successfully!');
                Session::flash('m-class', 'alert-success');
                return redirect()->back();
            }else{
                Session::flash('message', 'Data Delete Failed!');
                Session::flash('m-class', 'alert-danger');
                return redirect()->back();
            }

        }
    }



}
