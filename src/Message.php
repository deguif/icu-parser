<?php

namespace Deguif\Icu;

class Message
{
    /** @var MessageArgument[] */
    private array $arguments = [];
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return MessageArgument[]
     */
    public function getArguments(bool $nested = false): array
    {
        $arguments = $this->arguments;

        if ($nested) {
            foreach ($this->arguments as $argument) {
                if ($argument instanceof MessageArgumentSelection && $argument->getArguments()) {
                    $arguments = \array_merge($arguments, $argument->getArguments($nested));
                }
            }
        }

        return $arguments;
    }

    public function hasNestedArguments(): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof MessageArgumentSelection && $argument->getArguments()) {
                return true;
            }
        }

        return false;
    }

    public function addArgument(MessageArgument $argument): self
    {
        $this->arguments[] = $argument;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
