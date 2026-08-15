<?php

declare(strict_types=1);

/**
 * This file is part of the LongitudeOne GeoParser project.
 *
 * PHP 8.3 | 8.4 | 8.5
 *
 * Copyright LongitudeOne - Alexandre Tranchant - Derek J. Lambert.
 * Copyright 2024-2026.
 */

namespace LongitudeOne\Geo\String\Internal;

use Doctrine\Common\Lexer\Token;
use LongitudeOne\Geo\String\Exception\LogicException;
use LongitudeOne\Geo\String\Lexer;

/**
 * Cursor over a tokenized coordinate input.
 *
 * @internal this adapter isolates Doctrine's lexer and is not part of the public API
 */
final class TokenStream
{
    /**
     * Doctrine lexer used to tokenize the input.
     */
    private Lexer $lexer;

    /**
     * Tokenize the input and position the cursor on its first token.
     *
     * @param Lexer|null $lexer optional lexer used for controlled internal tests
     */
    public function __construct(string $input, ?Lexer $lexer = null)
    {
        $this->lexer = $lexer ?? new Lexer($input);

        if (null !== $lexer) {
            $this->lexer->setInput($input);
        }

        $this->lexer->moveNext();
    }

    /**
     * Consume the current token after confirming its type.
     *
     * @return Token<int, int|string>
     */
    public function consume(int $type): Token
    {
        if (!$this->matches($type)) {
            throw new LogicException(sprintf('Cannot consume token type %d.', $type));
        }

        $this->lexer->moveNext();

        if (!$this->lexer->token instanceof Token) {
            // @codeCoverageIgnoreStart
            throw new LogicException('A consumed token must be available.');
            // @codeCoverageIgnoreEnd
        }

        return $this->lexer->token;
    }

    /**
     * Consume a cardinal token and return its direction details.
     */
    public function consumeCardinal(int $type): Cardinal
    {
        return Cardinal::fromToken((string) $this->consume($type)->value);
    }

    /**
     * Return the current token without consuming it.
     *
     * @return Token<int, int|string>|null
     */
    public function current(): ?Token
    {
        return $this->lexer->lookahead;
    }

    /**
     * Look ahead one token without changing the cursor position.
     *
     * @return Token<int, int|string>|null
     */
    public function glimpse(): ?Token
    {
        return $this->lexer->glimpse();
    }

    /**
     * Return the lexer literal associated with a token type.
     */
    public function literal(int $type): string
    {
        return (string) $this->lexer->getLiteral($type);
    }

    /**
     * Whether the current token has the given type.
     */
    public function matches(int $type): bool
    {
        return $this->lexer->isNextToken($type);
    }

    /**
     * Whether the current token has one of the given types.
     *
     * @param list<int> $types token types to match
     */
    public function matchesAny(array $types): bool
    {
        return $this->lexer->isNextTokenAny($types);
    }
}
