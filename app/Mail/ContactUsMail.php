<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $name;
    public $email;
    public $phone;
    public $msg;
    public $preferred_way;
    public function __construct($name, $email, $phone, $msg, $preferred_way)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->msg = $msg;
        $this->preferred_way = $preferred_way;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contactUsEmail')->subject('Contact Us')
        ->with([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'msg' => $this->msg,
            'preferred_way'  => $this->preferred_way,
        ]);
    }
}
