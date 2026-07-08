<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send {user} {age} {school?} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '向用户发送电子邮件';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        var_dump($this->argument());
        var_dump($this->option('queue'));
        echo '执行控制台命令';
    }
}
