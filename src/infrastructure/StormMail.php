<?php

namespace infrastructure;

class StormMail
{
    public string $subject;
    public string $body;
    public string $recipient;

    public function setSubject($subject): StormMail
    {
        $this->subject = $subject;

        return $this;
    }

    public function setBody($body): StormMail
    {
        $this->body = $body;

        return $this;
    }

    public function setRecipient($recipient): StormMail
    {
        $this->recipient = $recipient;

        return $this;
    }
}