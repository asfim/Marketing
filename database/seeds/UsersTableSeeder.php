<?php

use App\Branch;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        //1) Create Admin Role
        $role = ['name' => 'super-admin', 'display_name' => 'Super Admin', 'description' => 'Full Permission'];
        $role = Role::create($role);
        //2) Set Role Permissions
        // Get all permission, swift through and attach them to the role
        $permission = Permission::get();
        foreach ($permission as $key => $value) {
            $role->attachPermission($value);
        }

        // Create Branch
        $branch = ['branchName' => 'Main Office', 'branchAddress' => 'Wari, Dhaka'];
        $branch = Branch::create($branch);
        //3) Create Admin User
        //$user = ['name' => 'Admin User', 'email' => 'adminuser@test.com', 'password' => Hash::make('adminpwd')];
        $user  = [
            'userName'  => 'Super Admin',
            'userEmail' => 'admin@gmail.com',
            'password'  => bcrypt('123456'),
            'branchId'  => 1,
        ];
        $user = User::create($user);
        //4) Set User Role
        $user->attachRole($role);


//        foreach ($users as $key=>$value) {
//            User::create($value);
//        }
    }
}
