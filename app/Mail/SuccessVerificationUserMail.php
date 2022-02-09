<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SuccessVerificationUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $subject;

    private $appName;


    /**
     * Create a new url instance.
     *
     * @param string $appName
     * @param null $user
     * @internal param $subject
     * @internal param string $greeting
     * @internal param string $message
     * @internal param $url
     * @internal param $title
     */
    public function __construct($appName = 'inventory', $user = null)
    {

        if ($appName == 'inventory') {
            $this->from('service@zuragan.com', 'Zuragan');
            $this->subject = 'Akun Anda di Zuragan sudah aktif';
        } else {
            $this->from('service@invoicekilat.com', 'InvoiceKilat');
            $this->subject = 'Akun Anda di Invoice Kilat sudah aktif';
        }

        $this->appName = $appName;
        $this->user = $user;
    }

    /**
     * Build the url.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('process success verification mail job to ' . $this->user->email);

        $data = [
            'subject' => $this->subject,
            'app_name' => $this->appName,
            'user' => $this->user,
        ];

        return $this->view('mails.users.success_verification_id')->with('data', $data);
    }
}
