<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'location_details',
        'profile_id',
        'user_id'
    ];

    protected static $logFillable = true;
    protected static $logName = 'location';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    //TODO:: Normalize location system
    public function owner()
    {
        return $this->belongsTo(OwnerProfile::class,'profile_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
