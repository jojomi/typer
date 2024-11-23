<?php

declare(strict_types = 1);

namespace Jojomi\Typer;

use InvalidArgumentException;
use Webmozart\Assert\Assert;
use function array_key_exists;
use function implode;
use function is_array;
use function is_float;
use function is_int;
use function is_string;
use function json_encode;
use function sprintf;

/**
 * Array-Helper.
 */
class Arry
{

    /**
     * @param array<mixed> $input
     */
    public static function getString(array $input, string|int ...$key): ?string
    {
        $value = self::getRaw($input, ...$key) ?? null;
        if ($value === null) {
            return null;
        }

        return Str::fromMixed($value);
    }

    /**
     * @param array<mixed> $input
     */
    public static function getRequiredString(array $input, string|int ...$key): string
    {
        self::assertKey($input, ...$key);
        $result = self::getString($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @param array<mixed> $input
     */
    public static function getInt(array $input, string|int ...$key): ?int
    {
        $value = self::getRaw($input, ...$key);
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            if ((string)(int)$value === $value) {
                return (int)$value;
            }
        }

        if (is_int($value)) {
            return $value;
        }

        throw new InvalidArgumentException(sprintf('could not parse positive integer value: %s', json_encode($value)));
    }

    /**
     * @param array<mixed> $input
     */
    public static function getRequiredInt(array $input, string|int ...$key): int
    {
        self::assertKey($input, ...$key);
        $result = self::getInt($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @param array<mixed> $input
     *
     * @phpstan-return (0|positive-int)|null
     */
    public static function getNonNegativeInt(array $input, string|int ...$key): ?int
    {
        $value = self::getRaw($input, ...$key);
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            if ((string)(int)$value === $value) {
                $value = (int)$value;
                if ($value >= 0) {
                    return $value;
                }
            }
        }

        if (is_int($value)) {
            if ($value >= 0) {
                return $value;
            }
        }

        throw new InvalidArgumentException(
            sprintf('could not parse non-negative integer value: %s', json_encode($value)),
        );
    }

    /**
     * @param array<mixed> $input
     *
     * @phpstan-return 0|positive-int
     */
    public static function getRequiredNonNegativeInt(array $input, string|int ...$key): int
    {
        self::assertKey($input, ...$key);
        $result = self::getNonNegativeInt($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @param array<mixed> $input
     *
     * @return ?positive-int
     */
    public static function getPositiveInt(array $input, string|int ...$key): ?int
    {
        $value = self::getRaw($input, ...$key);
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            if ((string)(int)$value === $value) {
                $intValue = (int)$value;
                if ($intValue < 1) {
                    throw new InvalidArgumentException(sprintf('integer should be positive, but is %d', $intValue));
                }

                return $intValue;
            }
        }

        if (is_int($value)) {
            if ($value < 1) {
                throw new InvalidArgumentException(sprintf('integer should be positive, but is %d', $value));
            }

            return $value;
        }

        throw new InvalidArgumentException(sprintf('could not parse positive integer value: %s', json_encode($value)));
    }

    /**
     * @param array<mixed> $input
     *
     * @return positive-int
     */
    public static function getRequiredPositiveInt(array $input, string|int ...$key): int
    {
        self::assertKey($input, ...$key);
        $result = self::getPositiveInt($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @param array<mixed> $input
     */
    public static function getFloat(array $input, string|int ...$key): ?float
    {
        $value = self::getRaw($input, ...$key);
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            // TODO improve
            return (float)$value;
        }

        return match (true) {
            is_float($value) => $value,
            is_int($value) => (float)$value,
            default => throw new InvalidArgumentException(
                sprintf(
                    'could not parse float value from %s',
                    Str::fromMixed($value),
                ),
            ),
        };
    }

    /**
     * @param array<mixed> $input
     */
    public static function getRequiredFloat(array $input, string|int ...$key): float
    {
        self::assertKey($input, ...$key);
        $result = self::getFloat($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public static function asStringMap(mixed $row): array
    {
        Assert::isArray($row);
        foreach ($row as $k => $v) {
            Assert::string($k);
            $row[$k] = $v;
        }

        return $row; // @phpstan-ignore-line
    }

    /**
     * @param array<mixed> $input
     *
     * @return array<mixed>
     */
    public static function getArray(array $input, string|int ...$key): ?array
    {
        $value = self::getRaw($input, ...$key);
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException('could not parse array at ' . implode('.', $key));
        }

        return $value;
    }

    /**
     * @param array<mixed> $input
     *
     * @return array<mixed>
     */
    public static function getRequiredArray(array $input, string|int ...$key): array
    {
        self::assertKey($input, ...$key);
        $result = self::getArray($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @param array<mixed> $input
     *
     * @return array<string, mixed>
     */
    public static function getStringMap(array $input, string|int ...$key): ?array
    {
        $value = self::getRaw($input, ...$key);
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException('could not parse array at ' . implode('.', $key));
        }

        $result = [];

        foreach ($value as $k => $v) {
            Assert::string($k);
            $result[$k] = $v;
        }

        return $result;
    }

    /**
     * @param array<mixed> $input
     *
     * @return array<string, mixed>
     */
    public static function getRequiredStringMap(array $input, string|int ...$key): array
    {
        self::assertKey($input, ...$key);
        $result = self::getStringMap($input, ...$key);
        Assert::notNull($result);

        return $result;
    }

    /**
     * @param array<mixed> $input
     */
    private static function getRaw(array $input, string|int ...$key): mixed
    {
        $currentArray = $input;
        foreach ($key as $k) {
            if (!is_array($currentArray)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'key %s (of %s) should be an array in array %s',
                        $k,
                        implode('.', $key),
                        json_encode($input),
                    ),
                );
            }
            if (!array_key_exists($k, $currentArray)) {
                return null;
            }
            $currentArray = $currentArray[$k];
        }

        return $currentArray;
    }

    /**
     * @param array<mixed> $input
     */
    public static function assertKey(array $input, string|int ...$key): mixed
    {
        $currentArray = $input;
        foreach ($key as $k) {
            if (!is_array($currentArray)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'key %s (of %s) should be an array in array %s',
                        $k,
                        implode('.', $key),
                        json_encode($input),
                    ),
                );
            }
            if (!array_key_exists($k, $currentArray)) {
                throw new InvalidArgumentException(
                    sprintf('key %s (of %s) not found in array %s', $k, implode('.', $key), json_encode($input)),
                );
            }
            $currentArray = $currentArray[$k];
        }

        return $currentArray;
    }
}