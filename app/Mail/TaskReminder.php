<?php

namespace App\Mail;

use App\Models\RepairReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskReminder extends Mailable
{
    use Queueable, SerializesModels;

    public RepairReminder $reminder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RepairReminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->markdown('emails.tasks.reminder')
            ->to($this->reminder->user->email, $this->reminder->user->full_name)
            ->subject('Reminder: ' . $this->reminder->repair->name);
    }
}
