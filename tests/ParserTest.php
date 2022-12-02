<?php

namespace Deguif\Icu\Tests;

use Deguif\Icu\Lexer;
use Deguif\Icu\MessageArgumentSelection;
use Deguif\Icu\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testPlaceholdersFor2Args(): void
    {
        $phrase = 'Hi {user} {num, plural, one {a} other {b}}!';
        $placeholders = ['{user}', '{num, plural, one {a} other {b}}'];
        $stream = new Lexer();
        $parser = new Parser();
        $message = $parser->parse($stream->tokenize($phrase), true);

        self::assertSame($phrase, $message->getText());

        $result = $message->getTextWithPlaceholders();
        self::assertMatchesRegularExpression('/^Hi \{arg[a-z\d]{13}\} \{arg[a-z\d]{13}\}!$/', $result);

        $i = 0;
        foreach ($message->getArguments(false) as $name => $argument) {
            self::assertSame(18, strlen($name));
            $result = str_replace($name, $placeholders[$i], $result);
            $i++;
        }
        self::assertSame($phrase, $result);
    }

    public function testPlaceholdersNested(): void
    {
        $phrase = 'Measure {system, select, imperial {{length, plural, one {one inch} other {# inches}}} other {{length, plural, one {1 cm} other {# cms}}}}. Tailing text';
        $stream = new Lexer();
        $parser = new Parser();
        $message = $parser->parse($stream->tokenize($phrase), true);

        self::assertSame($phrase, $message->getText());
        self::assertMatchesRegularExpression('/^Measure \{arg[a-z\d]{13}\}\. Tailing text$/', $message->getTextWithPlaceholders());

        $placeholders = ['{length, plural, one {one inch} other {# inches}}', '{length, plural, one {1 cm} other {# cms}}'];
        foreach ($message->getArguments(false) as $name => $argument) {
            self::assertSame(18, strlen($name));
            self::assertInstanceOf(MessageArgumentSelection::class, $argument);
            $selectors = $argument->getSelectors();
            self::assertCount(2, $selectors);
            foreach ($selectors as $i => $selector) {
                self::assertSame($placeholders[$i], $selector->getMessage()->getText());
                self::assertSame(
                    $selector->getMessage()->getText(),
                    str_replace(key($selector->getMessage()->getArguments(false)), $placeholders[$i], $selector->getMessage()->getTextWithPlaceholders())
                );
            }
        }
    }
}
