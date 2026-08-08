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

namespace LongitudeOne\Geo\String\Exception;

/**
 * RangeException.
 */
class RangeException extends \RangeException implements ExceptionInterface
{
    public const DEFAULT_MESSAGE = 'Unknown range exception';
    public const LATITUDE_MESSAGE = 'Latitude must be between -90 and 90';
    public const LATITUDE_OUT_OF_RANGE = 90;
    public const LONGITUDE_MESSAGE = 'Longitude must be between -180 and 180';
    public const LONGITUDE_OUT_OF_RANGE = 180;
    public const MINUTES_MESSAGE = 'Minutes must be between 0 and 59';
    public const MINUTES_OUT_OF_RANGE = 3600;
    public const SECONDS_MESSAGE = 'Seconds must be between 0 and 59';
    public const SECONDS_OUT_OF_RANGE = 60;

    /**
     * Maximum length of the value embedded in the exception message.
     */
    private const MAX_VALUE_LENGTH = 100;

    public function __construct(string $value, int $code, ?\Throwable $previous = null)
    {
        $message = sprintf('[RangeException] %s, got "%s".', $this->setMessage($code), $this->sanitizeValue($value));

        parent::__construct($message, $code, $previous);
    }

    /**
     * Strip control characters (CR, LF, ...) and truncate a value before it is embedded in the exception message.
     *
     * Prevents a large or crafted input from inflating the exception message size or forging fake log lines
     * when that message is logged as-is.
     */
    private function sanitizeValue(string $value): string
    {
        $value = (string) preg_replace('/[\x00-\x1F\x7F]/', ' ', $value);

        if (strlen($value) > self::MAX_VALUE_LENGTH) {
            return substr($value, 0, self::MAX_VALUE_LENGTH).'...';
        }

        return $value;
    }

    private function setMessage(int $code): string
    {
        return match ($code) {
            self::LATITUDE_OUT_OF_RANGE => self::LATITUDE_MESSAGE,
            self::LONGITUDE_OUT_OF_RANGE => self::LONGITUDE_MESSAGE,
            self::SECONDS_OUT_OF_RANGE => self::SECONDS_MESSAGE,
            self::MINUTES_OUT_OF_RANGE => self::MINUTES_MESSAGE,
            default => self::DEFAULT_MESSAGE,
        };
    }
}
