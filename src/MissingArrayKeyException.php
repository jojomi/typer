<?php

declare(strict_types=1);

namespace Jojomi\Typer;

use Exception;
use Throwable;

use function json_encode;
use function sprintf;
use function trim;

/**
 * Exception für fehlenden Array-Key, der aber erwartet ist.
 */
class MissingArrayKeyException extends Exception
{
    /**
     * @param array<array-key, mixed> $array
     * @param ?string $message
     * @param int $code
     */
    public function __construct(array $array, string|int $path, ?string $message = null, int $code = 1144, ?Throwable $previous = null)
    {
        $message = sprintf('Array-Key %s fehlt unerwartet (Array-Daten: %s) %s', $path, json_encode($array), $message);
        parent::__construct(trim($message), $code, $previous);
    }
}
