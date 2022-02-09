<?php

namespace App\Mails;

use App\Interfaces\Mailable;
use Config\ServiceContainer;
use Swift_Mailer;

class Mail
{
    private string $to;

    private function __construct(Swift_Mailer $mailer, string $to)
    {
        $this->mailer = $mailer;
        $this->to = $to;
    }

    /**
     * Prepare the mail service
     *
     * @param string $to
     *
     * @return self
     */
    public static function to(string $to): self
    {
        $mailer = ServiceContainer::get(Swift_Mailer::class);
        $to = $to;

        return new static($mailer, $to);
    }

    /**
     * Sends the email
     *
     * @return void
     */
    public function send(Mailable $mailable): void
    {
        $this->mailer->send($mailable->build($this->to));
    }
}