<?php

namespace Deguif\Icu;

class MessageArgument
{
    private string $name;
    private bool $numeric;

    public function __construct(string $name, bool $numeric)
    {
        $this->name = $name;
        $this->numeric = $numeric;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNumeric(): bool
    {
        return $this->numeric;
    }
}
