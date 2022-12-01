<?php

namespace Deguif\Icu\Tests;

use Deguif\Icu\MessageArgument;
use PHPUnit\Framework\TestCase;

class MessageArgumentTest extends TestCase
{
    /** @dataProvider numericProvider */
    public function testInstantiation(bool $isNumeric): void
    {
        $argument = new MessageArgument('name', $isNumeric);

        $this->assertSame('name', $argument->getName());
        $this->assertSame($isNumeric, $argument->isNumeric());
    }

    public function numericProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
