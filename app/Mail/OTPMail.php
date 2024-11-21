<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    // يمكن الوصول إليه داخل القالب الذي يعرض محتوى البريد الإلكتروني(صفحة ال view)
    public $otp;


    //تستقبل رمز OTP عند إنشاء كائن من OTPMail، ويتم تعيينه إلى الخاصية $otp ليصبح متاحًا داخل القالب.
    public function __construct($otp)
    {
        $this->otp=$otp;
    }

    public function build()
    {
        // تحديد subject الايميل ................
        // صفحة عرض محتويات الايميل المرسل موجودة في views ضمن مجلد emails
        return $this->subject('Your OTP Code')
                    ->view('emails.otp')
                    ->with([                   // تمرير المتحول العام otp
                        'otp'=>$this->otp
                    ]);
    }
}
