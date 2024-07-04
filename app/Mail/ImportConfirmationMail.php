<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $uploaderEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($uploaderEmail)
    {
        $this->uploaderEmail = $uploaderEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.confirmation')
            ->with([
                'email' => $this->uploaderEmail
            ])
            ->subject('Data Import Confirmation');
    }

}
