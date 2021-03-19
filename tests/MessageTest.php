<?php

namespace Deguif\Icu\Tests;

use Deguif\Icu\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testInstantiation(): void
    {
        $message = new Message('This is a test');

        $this->assertSame('This is a test', $message->getText());
    }
}
