<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ResetPasswordMailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    private $email;
    private $token;
    private $app;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param $token
     */
    public function __construct($app, $email, $token)
    {
        $this->email = $email;
        $this->token = $token;
        $this->app = $app;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new ResetPasswordMail($this->app, $this->token));
    }
}
