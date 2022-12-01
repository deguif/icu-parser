<?php

namespace Deguif\Icu;

class Message
{
    /** @var MessageArgument[] */
    private array $arguments = [];
    private string $text;
    private ?string $textWithPlaceholder;

    public function __construct(
        string $text,
        ?string $textWithPlaceholder = null,
    )
    {
        $this->text = $text;
        $this->textWithPlaceholder = $textWithPlaceholder;
    }

    /**
     * @return array<string, MessageArgument>
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

    public function addArgument(MessageArgument $argument, string $placeholder): self
    {
        $this->arguments[$placeholder] = $argument;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function withPlaceholder(string $argumentText, string $placeholder): self
    {
        $new = clone $this;
        $new->textWithPlaceholder = str_replace($argumentText, $placeholder, $this->textWithPlaceholder ?? $this->text);

        return $new;
    }

    public function getTextWithPlaceholders(): ?string
    {
        return $this->textWithPlaceholder;
    }
}
