<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerificationInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Zuragan Invitation';
    private $organizationName = '';
    private $url;
    private $user;


    /**
     * Create a new message instance.
     *
     * @param $subject
     * @param string $organizationName
     * @param $url
     * @param null $user
     */
    public function __construct($subject, $organizationName = '', $url, $user = null)
    {

        $this->subject = $subject;
        $this->organizationName = $organizationName;
        $this->url = $url;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('invitation email sent.');

        return $this
            ->view('mails.verification_user_invitation')
            ->with('data', [
                'organization_name' => $this->organizationName,
                'url' => $this->url,
                'username' => $this->user->username
            ]);
    }
}
