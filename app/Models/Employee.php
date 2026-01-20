<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employeeId',
        'employeeName',
        'employeeEmail',
        'employeePhone',
        'employeeAddress',
        'nationalId',
        'joiningDate',
        'salary',
        'photo'
    ];
}
