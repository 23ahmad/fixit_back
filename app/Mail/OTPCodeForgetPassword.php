<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPCodeForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $otp_password;

    public function __construct($otp_password)
    {
        $this->otp_password=$otp_password;
    }

    public function build()
    {
        return $this->subject('Your OTP Code For Reset Password')
                    ->view('emails.otp_code_password')
                    ->with([
                        'otp_password'=>$this->otp_password
                    ]);
    }


}
