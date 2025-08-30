<?php

declare(strict_types=1);

namespace Jojomi\Typer;

class Type {
    public static function print(mixed $input): string
    {
        if ($input === null) {
            return 'null';
        }
        if (is_int($input)) {
            return 'int';
        }
        if (is_float($input)) {
            return 'float';
        }
        if (is_string($input)) {
            return 'string';
        }
        if (is_bool($input)) {
            return 'bool';
        }
        if (is_array($input)) {
            return 'array';
        }
        if (is_object($input)) {
            return $input::class; // FQCN
        }
        if (is_resource($input)) {
            return 'resource';
        }

        return gettype($input);
    }
}
