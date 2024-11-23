<?php

declare(strict_types = 1);

namespace Jojomi\Typer;

use RuntimeException;
use Stringable;
use function is_string;
use function json_encode;

/**
 * Provides safe ways to convert strings with proper types.
 */
class Str
{
    public static function fromMixed(mixed $input): string
    {
        return match (true) {
            is_string($input) => $input,
            $input instanceof Stringable || is_float($input) => (string)$input,
            is_array($input) => self::handleArray($input),
            default => self::handleJson($input),
        };
    }

    public static function fromMixedOrNull(mixed $param): ?string
    {
        if ($param === null) {
            return null;
        }

        return self::fromMixed($param);
    }

    /**
     * @param array<mixed> $input
     */
    private static function handleArray(array $input): string
    {
        $data = array_map(static fn (mixed $m) => self::fromMixed($m), $input);

        return sprintf('[%s]', implode(', ', $data));
    }

    private static function handleJson(mixed $input): string
    {
        $result = json_encode($input);
        if ($result === false) {
            throw new RuntimeException('could not encode to JSON');
        }

        return $result;
    }
}