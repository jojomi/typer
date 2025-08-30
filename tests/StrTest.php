<?php

declare(strict_types=1);

namespace Jojomi\Typer\Tests;

use Generator;
use Jojomi\Typer\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stringable;
use stdClass;

#[CoversClass(Str::class)]
class StrTest extends TestCase
{
    #[DataProvider('fromMixedProvider')]
    public function testFromMixed(mixed $input, string $expected): void
    {
        self::assertSame($expected, Str::fromMixed($input));
    }

    /**
     * @return Generator<string, array{0: mixed, 1: string}>
     */
    public static function fromMixedProvider(): Generator
    {
        yield 'string passthrough' => ['hello', 'hello'];
        yield 'float cast' => [1.25, '1.25'];
        yield 'int via json' => [42, '42'];
        yield 'bool true via json' => [true, 'true'];
        yield 'bool false via json' => [false, 'false'];

        yield 'Stringable' => [new class implements Stringable {
            public function __toString(): string { return 'stringable-value'; }
        }, 'stringable-value'];

        yield 'empty array' => [[], '[]'];
        yield 'simple array' => [['a' => 1, 'b' => 'x'], '[a => 1, b => x]'];
        yield 'array with numeric keys' => [[0 => 'a', 2 => 'b'], '[0 => a, 2 => b]'];
        yield 'nested array' => [['x' => ['y' => 2]], '[x => [y => 2]]'];

        $emptyObj = new stdClass();
        yield 'empty object via json' => [$emptyObj, '{}'];

        $objWithData = new stdClass();
        $objWithData->a = 1;
        $objWithData->b = 'x';
        yield 'object with data via json' => [$objWithData, '{"a":1,"b":"x"}'];
    }

    public function testFromMixedOrNullWithNull(): void
    {
        self::assertNull(Str::fromMixedOrNull(null));
    }

    #[DataProvider('fromMixedProvider')]
    public function testFromMixedOrNullDelegates(mixed $input, string $expected): void
    {
        self::assertSame($expected, Str::fromMixedOrNull($input));
    }

    public function testFromMixedThrowsWhenJsonEncodeFails(): void
    {
        // Create a recursive object that forces json_encode to fail
        $o = new stdClass();
        $o->self = $o;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('could not encode to JSON');

        Str::fromMixed($o);
    }
}