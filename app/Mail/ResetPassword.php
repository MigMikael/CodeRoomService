<?php

namespace App\Mail;

use App\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $username;
    protected $temp_pass;

    /**
     * Create a new message instance.
     *
     * @param Teacher $teacher
     * @param $temp_pass
     */
    public function __construct($username, $temp_pass)
    {
        $this->username = $username;
        $this->temp_pass = $temp_pass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset_password')->with([
            'username' => $this->username,
            'password' => $this->temp_pass
        ]);
    }
}
