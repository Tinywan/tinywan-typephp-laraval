<?php

namespace App\Console;

use App\Console\Commands\SendEmails;
use App\Jobs\HelloJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $this->scheduleMinute($schedule);
        $this->scheduleHourly($schedule);
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

    /**
     * @desc scheduleMinute
     * @param Schedule $schedule
     * @author Tinywan(ShaoBo Wan)
     */
    private function scheduleMinute(Schedule $schedule)
    {
        /** 调度队列任务 */
        $schedule->job(new HelloJob(1))->everyMinute();

        /** 调度 Artisan 命令 （php artisan email:send tinywan 24）*/
        $schedule->command('email:send',[
            'user' => 'tinywan',
            'age' => 24,
            'school' => '清华大学',
            '--queue' => true,
        ])->everyMinute()->onOneServer();
    }

    /**
     * @desc scheduleHourly
     * @param Schedule $schedule
     * @author Tinywan(ShaoBo Wan)
     */
    private function scheduleHourly(Schedule $schedule)
    {
        $schedule->job(new HelloJob(1))->hourly();
    }
}
