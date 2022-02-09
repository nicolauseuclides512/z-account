<?php

namespace App\Jobs;

use App\Mail\SuccessVerificationUserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SuccessVerificationUserMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $appName;

    /**
     * Create a new job instance.
     * @param $appName
     * @param $user
     * @internal param $subject
     * @internal param $greeting
     * @internal param $message
     * @internal param $organization
     */
    public function __construct($appName, $user)
    {
        $this->user = $user;
        $this->appName = $appName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new SuccessVerificationUserMail(
            $this->appName,
            $this->user
        ));

        Log::info('send success verification mail job to ' . $this->user->email);

    }
}
