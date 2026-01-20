<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineerTipsStatement extends Model
{
    protected $fillable = [
        'transaction_id',
        'posting_date',
        'description',
        'table_name',
        'debit',
        'credit',
        'balance',
        'customer_id',
    ];


    public function balance(){
        return $this->sum(DB::raw('credit - debit'));
    }

    public function balanceText(){
        $s_balance = $this->balance();
        if($s_balance < 0) {
            $balance = number_format(abs($s_balance),2).' TK Advance';
        } elseif($s_balance > 0) {
            $balance = number_format(abs($s_balance),2).' TK Due';
        } else {
            $balance = '0 TK';
        }
        return $balance;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
