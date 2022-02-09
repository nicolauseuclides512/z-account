<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerificationUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public $user;

    public $subject;

    private $greeting;

    private $message;

    /**
     * Create a new url instance.
     *
     * @param $subject
     * @param string $greeting
     * @param string $message
     * @param $url
     * @param null $user
     * @internal param $title
     */
    public function __construct($subject, $greeting = 'Hello', $message = '', $url, $user = null)
    {
        $this->greeting = $greeting;
        $this->subject = $subject;
        $this->message = $message;
        $this->url = $url;
        $this->user = $user;
    }

    /**
     * Build the url.
     *
     * @return $this
     */
    public function build()
    {

        Log::info('process verification mail job to ' . $this->user->email);

        $data = [
            'subject' => $this->subject,
            'message' => $this->message,
            'greeting' => $this->greeting,
            'user' => $this->user,
            'url' => $this->url
        ];

        return $this->view('mails.verification_user_in')->with('data', $data);
    }
}
