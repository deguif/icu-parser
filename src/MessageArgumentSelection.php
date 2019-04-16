<?php

namespace Deguif\Icu;

class MessageArgumentSelection extends MessageArgument
{
    /** @var MessageArgumentSelector[] */
    private $selectors = [];
    private $type;
    private $offset;

    public function __construct(MessageArgument $prototype, string $type, ?int $offset = null)
    {
        $this->type = $type;
        $this->offset = $offset;

        parent::__construct($prototype->getName(), $prototype->isNumeric());
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return MessageArgumentSelector[]
     */
    public function getSelectors(): array
    {
        return $this->selectors;
    }

    public function addSelector(MessageArgumentSelector $selector): self
    {
        $this->selectors[] = $selector;

        return $this;
    }

    public function getArguments(bool $nested = false): array
    {
        $arguments = [];

        foreach ($this->selectors as $selector) {
            if ($selector->getMessage()->getArguments()) {
                $arguments = \array_merge($arguments, $selector->getMessage()->getArguments($nested));
            }
        }

        return $arguments;
    }
}
