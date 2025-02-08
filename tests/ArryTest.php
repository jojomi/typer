<?php

declare(strict_types=1);

namespace Jojomi\Typer\Tests;

use Generator;
use Jojomi\Typer\Arry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Arry::class)]
class ArryTest extends TestCase
{
    #[DataProvider('mapDataProvider')]
    public function testAssertMap(mixed $data, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValueError::class);
        } else {
            $this->expectNotToPerformAssertions();
        }

        Arry::assertMap($data);
    }

    #[DataProvider('mapDataProvider')]
    public function testIsMap(mixed $data, bool $isValid): void
    {
        self::assertSame($isValid, Arry::isMap($data));
    }

    /**
     * @return Generator<string, array{0: mixed, 1: bool}>
     */
    public static function mapDataProvider(): Generator
    {
        yield 'valid array' => [['key1' => 'value1', 'key2' => 2], true];
        yield 'invalid array' => [['key1' => 'value1', 2 => 'value2'], false];
        yield 'empty array' => [[], true];
        yield 'no array' => [24, false];
    }

    #[DataProvider('arrayDataProvider')]
    public function testAssertArray(mixed $data, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValueError::class);
        } else {
            $this->expectNotToPerformAssertions();
        }

        Arry::assertArray($data);
    }

    /**
     * @return Generator<string, array{0: mixed, 1: bool}>
     */
    public static function arrayDataProvider(): Generator
    {
        yield 'valid assoc array' => [['key1' => 'value1', 'key2' => 2], true];
        yield 'valid array' => [['value1', 2], true];
        yield 'empty array' => [[], true];
        yield 'int' => [24, false];
        yield 'string' => ['twelve', false];
    }
}
