<?php

namespace App\Interfaces;

use Swift_Message;

interface Mailable
{
    /** 
     * Builds the message to be sent
     *
     * @param string $to
     *
     * @return Swift_Message
     */
    public function build(string $to): Swift_Message;
}