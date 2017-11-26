<?php

namespace App\Mail;

use App\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InformCreateAccount extends Mailable
{
    use Queueable, SerializesModels;

    protected $teacher;
    protected $temp_pass;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Teacher $teacher, $temp_pass)
    {
        $this->teacher = $teacher;
        $this->temp_pass = $temp_pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.owner.inform')
            ->with([
                'username' => $this->teacher->username,
                'password' => $this->temp_pass
            ]);
    }
}
