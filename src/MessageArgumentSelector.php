<?php

namespace Deguif\Icu;

class MessageArgumentSelector
{
    private $message;
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}
