<?php

namespace App\Mails;

use App\Interfaces\Mailable;
use \Swift_Message;

class StockQuoteMail implements Mailable
{
    public function __construct($content)
    {
        $this->subject = 'Hello from PHP Challenge';
        $this->from_name = $_ENV['MAILER_FROM_NAME'] ?? 'PHP Challenge';
        $this->from_email = $_ENV['MAILER_FROM_EMAIL'] ?? 'phpchallenge@jobsity.io';
        $this->content = json_encode($content, JSON_PRETTY_PRINT);
    }

    /** 
     * Builds the message to be sent
     *
     * @param string $to
     *
     * @return Swift_Message
     */
    public function build(string $to): Swift_Message
    {
        $message = new Swift_Message($this->subject);

        return $message->setFrom([$this->from_email => $this->from_name])
                        ->setTo([$to])
                        ->setBody($this->content);
    }
}