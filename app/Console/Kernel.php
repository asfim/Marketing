<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //all command paths here
        '\App\Console\Commands\UpdateDepreciation',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //increase depreciation daily for asset
        $schedule->call(function () {
            $assets = DB::table('assets')->get();
            foreach($assets as $asset)
            {
                $daily_dep = $asset->depreciation /365;
                $updated_dep = $asset->depreciated_amount + $daily_dep;
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

        })->daily();

        $schedule->command('command:updateDepreciation')
            ->daily();

        //CLEAN ACTIVITY LOG
        $schedule->command('activitylog:clean')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
