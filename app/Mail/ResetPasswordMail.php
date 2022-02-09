<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $app;
    public $subject = 'Atur Ulang Password';

    /**
     * Create a new notification instance.
     *
     * @param $token
     */
    public function __construct($app, $token)
    {
        $this->token = $token;
        $this->app = $app;

    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.reset_password')->with('actionUrl', url("password/reset/{$this->token}?aid={$this->app}"));
    }
}
