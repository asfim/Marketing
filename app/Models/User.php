<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'email', 'password', 'roleId', 'branchId', 'activeStatus', 'verifyToken'
    ];

    protected static $logAttributes = ['name', 'email', 'roleId', 'branchId', 'activeStatus'];
//    protected static $logFillable = true;
    protected static $logName = 'user';
//    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }


//    public function roles()
//    {
//        return $this->belongsToMany(Role::class, 'role_users');
//    }
//
    /** This is for role access */
//    public function authorizeRoles($roles)
//    {
//        if ($this->hasAnyRole($roles)) {
//            return true;
//        }
//        abort(401, 'This action is unauthorized.');
//    }
//    public function hasAnyRole($roles)
//    {
//        if (is_array($roles)) {
//            foreach ($roles as $role) {
//                if ($this->hasRole($role)) {
//                    return true;
//                }
//            }
//        } else {
//            if ($this->hasRole($roles)) {
//                return true;
//            }
//        }
//        return false;
//    }
//    public function hasRole($role)
//    {
//        if ($this->roles()->where('roleName', $role)->first()) {
//            return true;
//        }
//        return false;
//    }

//    private function checkIfUserRole($user_role)
//    {
//        return (strtolower($user_role) == strtolower($this->roles[0]->roleName)) ? true : null;
//    }
//
//    public function hasRole($roles){
//        if(is_array($roles)) {
//            foreach ($roles as $need_role){
//                if($this->checkIfUserRole($need_role)){
//                    return true;
//                }
//            }
//        }else{
//            return $this->checkIfUserRole($roles);
//        }
//
//        return false;
//    }

}
