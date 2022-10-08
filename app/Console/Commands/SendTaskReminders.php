<?php

namespace App\Console\Commands;

use App\Mail\TaskReminder;
use App\Models\RepairReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends all task reminders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $taskReminders = RepairReminder::where('reminder', '<', Carbon::now())->get();
        foreach ($taskReminders as $reminder) {
            Mail::send(new TaskReminder($reminder));
        }

        return 0;
    }
}
