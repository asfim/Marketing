<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'extra_phone_no','user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'supplier';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function supplierStatements(){
        return $this->hasMany(SupplierStatement::class,'supplier_id');
    }

    public function balance(){
     
        return $this->supplierStatements()->sum(DB::raw('credit - debit'));
    }

    public function adjustedBalance(){
        return $this->balance();
    }

    public function balanceText(){
    $s_balance = $this->balance();

    if($s_balance < 0) {
        // Advance (Blue Background)
        $balance = '<span style="background:#007bff;color:#fff;padding:3px 6px;border-radius:4px;">'
                    . number_format(abs($s_balance), 2) . ' TK Advance</span>';
    } 
    elseif($s_balance > 0) {
        // Due (Red Background)
        $balance = '<span style="background:#dc3545;color:#fff;padding:3px 6px;border-radius:4px;">'
                    . number_format(abs($s_balance), 2) . ' TK Due</span>';
    } 
    else {
        // Zero (Grey background)
        $balance = '<span style="background:#6c757d;color:#fff;padding:3px 6px;border-radius:4px;">0 TK</span>';
    }

    return $balance;
}


    public function payments(){
        return $this->hasMany(SupplierPayment::class,'supplier_id');
    }

    public function purchases(){
        return $this->hasMany(ProductPurchase::class,'supplier_id')
            // ->where('check_status', 0)
            ->orderBy('check_status','ASC')
            ->orderBy('received_date','DESC');
    }

    public function checkedPurchases(){
        return $this->hasMany(ProductPurchase::class,'supplier_id')
            ->select(DB::raw('bill_no,count(*)as tor,MAX(received_date) as received_date, 
            MAX(dmr_no) as dmr_no, MAX(check_status) as check_status, MAX(chalan_no) as chalan_no, 
            MAX(supplier_id) as supplier_id, MAX(product_name_id) as product_name_id,
            sum(material_cost) as material_cost, sum(total_material_cost) as total_material_cost, 
            sum(truck_rent) as truck_rent, sum(unload_bill) as unload_bill, sum(product_qty) as product_qty'))
            ->groupBy('bill_no')
            ->where('check_status', 1)
            ->orderBy('received_date','DESC');
    }
}
