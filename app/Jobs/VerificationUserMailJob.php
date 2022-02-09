<?php

namespace App\Jobs;

use App\Mail\VerificationUserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationUserMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $subject;

    private $greeting;

    private $message;

    private $url;

    /**
     * Create a new job instance.
     * @param $subject
     * @param $greeting
     * @param $message
     * @param $url
     * @param $user
     * @internal param $organization
     */
    public function __construct($subject, $greeting, $message, $url, $user)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('send verification mail job to ' . $this->user->email);

        Mail::to($this->user->email)->send(new VerificationUserMail(
            $this->subject,
            $this->greeting,
            $this->message,
            $this->url,
            $this->user
        ));
    }
}
