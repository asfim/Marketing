<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'extra_phone_no', 'user_id',
    ];

    protected static $logFillable = true;
    protected static $logName = 'customer';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    public function customerStatements(){
        return $this->hasMany(CustomerStatement::class,'customer_id');
    }

    public function balance(){
        return $this->customerStatements()->sum(DB::raw('credit - debit'));
    }
     public function debitSum(){
        return $this->customerStatements()->sum(DB::raw('debit'));
    }
       public function creditSum(){
        return $this->customerStatements()->sum(DB::raw('credit'));
    }

    public function balanceText() {
        // Calculate the current balance
        $s_balance = $this->balance();

        // Calculate total_billable for this customer
        $total_billable = 0;
        $uncheck_challans = ProductSale::where('customer_id', $this->id)
            ->where('status', 1)
            ->get();

//        foreach ($uncheck_challans as $challan) {
//            $total_billable += ($challan->cuM * 35.315) * $challan->mix_design->rate;
//        }
        $total_billable = 0;

        foreach ($uncheck_challans as $challan) {
            $qty_cft = $challan->cuM * 35.315;
            $rate = $challan->rate > 0 ? $challan->rate : $challan->mix_design->rate;
            $total_billable += $qty_cft * $rate;
        }


        // Adjust the balance with the total_billable amount
        $adjusted_balance = $s_balance + $total_billable;

        // Format the output based on the adjusted balance
        if ($adjusted_balance < 0) {
            return '<span style="background:#007bff;color:#fff;padding:6px 12px;border-radius:6px;">'
                    . number_format(abs($adjusted_balance), 2) . ' TK Advance</span>';
        } elseif ($adjusted_balance > 0) {
            return '<span style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:6px;">'
                    . number_format(abs($adjusted_balance), 2) . ' TK Due</span>';
        } else {
            return '<span style="background:#6c757d;color:#fff;padding:6px 12px;border-radius:6px;">0 TK</span>';
        }

                return $balance;
            }


            public function balanceTextF($from_date = null, $to_date = null) {
    // Calculate the current balance
    $s_balance = $this->balance();

    // Start building the query for ProductSale
    $query = ProductSale::where('customer_id', $this->id)
        ->where('status', 1);

    // Apply date range filter if provided
    if ($from_date) {
        $query->whereDate('created_at', '>=', $from_date);
    }
    if ($to_date) {
        $query->whereDate('created_at', '<=', $to_date);
    }

    // Get the filtered challans
    $uncheck_challans = $query->get();

    // Calculate total_billable
    $total_billable = 0;
    foreach ($uncheck_challans as $challan) {
        $qty_cft = $challan->cuM * 35.315;
        $rate = $challan->rate > 0 ? $challan->rate : $challan->mix_design->rate;
        $total_billable += $qty_cft * $rate;
    }

    // Adjust the balance with the total_billable amount
    $adjusted_balance = $s_balance + $total_billable;

    // Format the output
    if ($adjusted_balance < 0) {
        return '<span style="background:#007bff;color:#fff;padding:6px 12px;border-radius:6px;">'
                . number_format(abs($adjusted_balance), 2) . ' TK Advance</span>';
    } elseif ($adjusted_balance > 0) {
        return '<span style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:6px;">'
                . number_format(abs($adjusted_balance), 2) . ' TK Due</span>';
    } else {
        return '<span style="background:#6c757d;color:#fff;padding:6px 12px;border-radius:6px;">0 TK</span>';
    }
}





    public function projects()
    {
        return $this->hasMany(CustomerProject::class,'customer_id','id');
    }

    public function mixDesigns()
    {
        return $this->hasMany(MixDesign::class,'customer_id','id');
    }

    public function challans()
    {
        return $this->hasMany(ProductSale::class,'customer_id','id');
    }
    public function demoChallans()
    {
        return $this->hasMany(DemoProductSale::class,'customer_id','id');
    }

    public function payments(){
        return $this->hasMany(CustomerPayment::class,'customer_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class,'customer_id','id')->orderBy('bill_date');
    }

    public function demoBills()
    {
        return $this->hasMany(DemoBill::class,'customer_id','id')->orderBy('bill_date');
    }

}
