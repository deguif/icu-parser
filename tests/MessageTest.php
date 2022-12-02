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

    /** @dataProvider placeholderProvider */
    public function testWithPlaceholder(?string $basePlaceholderText, string $expected): void
    {
        $message = new Message('Test, {john}! {count, number}', $basePlaceholderText);

        self::assertSame($basePlaceholderText, $message->getTextWithPlaceholders());
        $message = $message->withPlaceholder('{john}', '{uniqId}');
        self::assertSame($expected, $message->getTextWithPlaceholders());
    }

    public function placeholderProvider(): array
    {
        return [
            [null, 'Test, {uniqId}! {count, number}'],
            ['Test, {john}! {uniq2}', 'Test, {uniqId}! {uniq2}'],
        ];
    }
}
