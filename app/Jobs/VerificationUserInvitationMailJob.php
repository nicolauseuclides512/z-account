<?php

namespace App\Jobs;

use App\Mail\VerificationInvitationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationUserInvitationMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $subject = '';
    private $organizationName;
    private $url;
    private $user;

    /**
     * Create a new job instance.
     * @param string $subject
     * @param $organizationName
     * @param $url
     * @param $user
     * @internal param $organization
     */
    public function __construct($subject = '', $organizationName, $url, $user)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->organizationName = $organizationName;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('send verification invitation mail job to ' . $this->user->email);

        Mail::to($this->user->email)
            ->send(new VerificationInvitationMail(
                $this->subject,
                $this->organizationName,
                $this->url,
                $this->user
            ));
    }
}
