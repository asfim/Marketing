<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class UpdateDepreciation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateDepreciation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Depreciation Updated';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Dhaka");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $assets = DB::table('assets')->get();
        foreach($assets as $asset)
        {
            $daily_dep = $asset->depreciation /365;
            $purchase_date = date_create($asset->purchase_date);
            $present_date = date_create(date('Y-m-d'));
            $total_day = date_diff($purchase_date, $present_date);

//            print_r($total_day->format('%R%a'));die;
            if($total_day->format('%R%a')>0)
            {
                $updated_dep = $total_day->format('%R%a') * $daily_dep;
                $total_depreciation = $asset->purchase_amount - $asset->salvage_value;
                if($updated_dep >= $total_depreciation)
                {
                    return false;
                }
                else {
                    $data = array();
                    $data['depreciated_amount'] = $updated_dep;
                    DB::table('assets')->where('id',$asset->id)->update($data);
                }

            }

        }
    }
}
