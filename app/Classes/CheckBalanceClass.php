<?php
namespace App\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\BankStatement;
use App\Models\CashStatement;
use App\Models\SupplierStatement;
use App\Models\BankInfo;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Branch;
use DB;

class CheckBalanceClass
{
    public function checkCashBalance($amt,$route)
    {
        //checking cash balance
        $cash_statements = new CashStatement();
        $cash_rows = $cash_statements->orderBy('id','ASC')->get();
        $cash_bal = 0;
        foreach ($cash_rows as $statement)
        {
            $cash_bal += $statement->credit - $statement->debit;
        }
        
        //echo $cash_bal.' '.$amt.' '.$route; dd();
        $row = CashStatement::max('id');
        if($row == "")
        {
            Session::put(['message' => 'No balance in cash', 'alert' => 'alert-danger']);
            return Redirect::to($route);
        }
        
       elseif($amt > $cash_bal)
        {
           Session::put(['message' => 'Insufficiant Balance in Cash', 'alert' => 'alert-danger']);
            return Redirect::to($route); 
        }
    }
}
?>