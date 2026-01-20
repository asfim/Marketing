<?php
/**
 * Created by PhpStorm.
 * User: Deelko 1
 * Date: 8/25/2021
 * Time: 4:27 PM
 */

    if (! function_exists('date_range_to_arr')){
        function date_range_to_arr($range){

            $range = explode(' - ',$range);
            $result[0] = array_key_exists(0,$range)?$range[0].' 00:00:00':null;
            $result[1] = array_key_exists(1,$range)?$range[1].' 23:59:59':null;
            return $result;

        }
    }

    if (! function_exists('cashBalance')){
        function cashBalance($branchId=null){
            $cash_balance = \App\Models\CashStatement::where('branchId',$branchId)->sum(DB::raw('credit - debit'));
            return $cash_balance;
        }
    }