<?php

namespace App\Console;

use App\Models\IntegrationShippingLog;
use App\Models\ShippingCompany;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function bootstrappers()
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         // convert pending shipping log to false
         $schedule->call( function () {
            IntegrationShippingLog::where( 'status',  'pending')
             ->whereRaw('NOW() > `added_at` + INTERVAL 3 MINUTE')
             ->update(['status'=>'false','response'=>'{"msg":"changed from pending to false by cronjob"}']);
            
            } )->everyMinute();
        $schedule->call( function () {
                ShippingCompany::whereNotNull('id')->update([
                    'today_orders'=>0,
                ]);
            } )->dailyAt('23:59');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
