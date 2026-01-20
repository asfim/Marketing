<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSallarySetting extends Model
{

    protected $fillable = [
        'transaction_id',
        'employeeId',
        'salary',
        'payment_mode',
        'employeeBonus',
        'totalSalary',
        'description',
        'sMonth',
        'paymentDate',
        'user_id'

    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employeeId', 'employeeId');
    }

    public function expenses()
    {
        return $this->hasOne(Expense::class, 'transaction_id', 'transaction_id');
    }

    public function bank_statements()
    {
        return $this->hasOne(BankStatement::class, 'transaction_id', 'transaction_id');
    }

    public function cash_statements()
    {
        return $this->hasOne(CashStatement::class, 'transaction_id', 'transaction_id');
    }


}
