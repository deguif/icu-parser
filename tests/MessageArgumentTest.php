<?php

namespace Deguif\Icu\Tests;

use Deguif\Icu\MessageArgument;
use PHPUnit\Framework\TestCase;

class MessageArgumentTest extends TestCase
{
    public function testInstantiation(): void
    {
        $argument = new MessageArgument('name', false);

        $this->assertSame('name', $argument->getName());
        $this->assertFalse($argument->isNumeric());
    }
}
