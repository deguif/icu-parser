<?php

namespace Deguif\Icu;

class Parser
{
    private bool $strict = false;

    public function parse(TokenStream $stream, bool $strict = false): Message
    {
        $this->strict = $strict;

        return $this->parseMessage($stream, 0);
    }

    private function parseMessage(TokenStream $stream, int $startOffset, ?int $limitOffset = null): Message
    {
        $tokens = $stream->getTokens();
        $limitOffset = $limitOffset ?? (\count($tokens) - 1);

        if (Token::TYPE_MSG_START !== $tokens[$startOffset]->getType()) {
            throw new \RuntimeException(\sprintf('Start token should be of type \'%s\'.', Token::TYPE_MSG_START));
        }

        if (Token::TYPE_MSG_LIMIT !== $tokens[$limitOffset]->getType()) {
            throw new \RuntimeException(\sprintf('Limit token should be of type \'%s\'.', Token::TYPE_MSG_LIMIT));
        }

        $startToken = $tokens[$startOffset];
        $limitToken = $tokens[$limitOffset];

        $message = new Message(\mb_substr($stream->getSource(), 0 === $startToken->getIndex() ? 0 : $startToken->getIndex() + 1, $limitToken->getIndex() - (0 === $startToken->getIndex() ? 0 : $startToken->getIndex() + 1)));

        for ($offset = $startOffset + 1; $offset < $limitOffset; ++$offset) {
            $token = $tokens[$offset];

            if (Token::TYPE_ARG_START !== $token->getType()) {
                continue;
            }

            $argument = $this->parseArgument($stream, $offset, $token->getLimit());
            $uid = '{' . uniqid('arg') . '}';
            $message->addArgument($argument, $uid);

            $replaced = substr(
                $stream->getSource(),
                $token->getIndex(),
                $tokens[$token->getLimit()]->getIndex() - $token->getIndex() +1
            );
            $message = $message->withPlaceholder($replaced, $uid);

            $offset = (int) $token->getLimit();
        }

        return $message;
    }

    private function parseArgument(TokenStream $stream, int $startOffset, int $limitOffset): MessageArgument
    {
        $tokens = $stream->getTokens();
        $offset = $startOffset + 1;

        if (Token::TYPE_ARG_START !== $tokens[$startOffset]->getType()) {
            throw new \RuntimeException(\sprintf('Start token should be of type \'%s\'.', Token::TYPE_ARG_START));
        }

        if (Token::TYPE_ARG_LIMIT !== $tokens[$limitOffset]->getType()) {
            throw new \RuntimeException(\sprintf('Limit token should be of type \'%s\'.', Token::TYPE_ARG_LIMIT));
        }

        if (!\in_array($tokens[$offset]->getType(), [Token::TYPE_ARG_NUMBER, Token::TYPE_ARG_NAME], true)) {
            throw new \RuntimeException(\sprintf('Next token should be of type \'%s\'.', \implode('\' or \'', [Token::TYPE_ARG_NUMBER, Token::TYPE_ARG_NAME])));
        }

        $argument = new MessageArgument($tokens[$offset]->getValue(), Token::TYPE_ARG_NUMBER === $tokens[$offset]->getType());

        if (\in_array($tokens[++$offset]->getType(), [Token::TYPE_ARG_DOUBLE, Token::TYPE_ARG_INT], true)) {
            $argument = new MessageArgumentSelection($argument, $tokens[$startOffset]->getValue(), $tokens[$offset++]->getValue());
        }

        for (; $offset < $limitOffset; ++$offset) {
            $token = $tokens[$offset];

            if (Token::TYPE_ARG_SELECTOR === $token->getType()) {
                if (\in_array($tokens[$offset + 1]->getType(), [Token::TYPE_ARG_DOUBLE, Token::TYPE_ARG_INT], true)) {
                    $selector = new MessageArgumentSelector($tokens[++$offset]->getValue());
                } else {
                    $selector = new MessageArgumentSelector($token->getValue());
                }

                $selector->setMessage($this->parseMessage($stream, ++$offset, $tokens[$offset]->getLimit()));
                $offset = (int) $tokens[$offset]->getLimit();

                if (!$argument instanceof MessageArgumentSelection) {
                    $argument = new MessageArgumentSelection($argument, $tokens[$startOffset]->getValue());
                }

                if ($this->strict && isset($argument->getSelectors()[$selector->getValue()])) {
                    throw new \RuntimeException(\sprintf('Selector already exists for value \'%s\'.', $selector->getValue()));
                }

                $argument->addSelector($selector);
            }
        }

        return $argument;
    }
}
