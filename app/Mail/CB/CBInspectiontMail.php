<?php

namespace App\Mail\CB;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CBInspectiontMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->certi_cb = $item['certi_cb'];
        $this->assessment = $item['assessment'];
   
        $this->url = $item['url'];
        $this->email = $item['email'];
        $this->email_cc = $item['email_cc'];
 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( config('mail.from.address'),config('mail.from.name') ) // $this->email
                   ->cc($this->email_cc)
                   ->subject('ยืนยันขอบข่ายการรับรองหน่วยรับรอง')
                   ->view('mail/CB.inspectiont')
                   ->with(['certi_cb' => $this->certi_cb,
                           'assessment' => $this->assessment,
                           'url' => $this->url
                          ]);
    }
}
