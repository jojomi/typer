<?php

declare(strict_types=1);

namespace Jojomi\Typer\Tests;

use Generator;
use InvalidArgumentException;
use Jojomi\Typer\Arry;
use Jojomi\Typer\MissingArrayKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;
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
// ... existing code ...
    public function testIsSet(): void
    {
        $data = ['a' => ['b' => ['c' => 1]]];
        self::assertTrue(Arry::isSet($data, 'a', 'b', 'c'));
        self::assertFalse(Arry::isSet($data, 'a', 'b', 'x'));

        $this->expectException(InvalidArgumentException::class);
        Arry::isSet(['a' => 1], 'a', 'b');
    }

    public function testGetRaw(): void
    {
        $data = ['x' => ['y' => ['z' => 9]]];
        self::assertSame(9, Arry::getRaw($data, 'x', 'y', 'z'));
        self::assertNull(Arry::getRaw($data, 'x', 'y', 'missing'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getRaw(['x' => 1], 'x', 'y');
    }

    public function testAssertKey(): void
    {
        $data = ['a' => ['b' => 2]];
        self::assertSame(2, Arry::assertKey($data, 'a', 'b'));

        $this->expectException(InvalidArgumentException::class);
        Arry::assertKey($data, 'a', 'c');
    }

    #[DataProvider('getIntProvider')]
    public function testGetInt(mixed $value, ?int $expected): void
    {
        self::assertSame($expected, Arry::getInt(['k' => $value], 'k'));
    }

    /**
     * @return Generator<string, array{0: mixed, 1: ?int}>
     */
    public static function getIntProvider(): Generator
    {
        yield 'int' => [12, 12];
        yield 'numeric string simple' => ['10', 10];
        yield 'numeric string leading zero accepted' => ['01', 1];
        yield 'zero string rejected' => ['0', null];
        yield 'double zero rejected' => ['00', null];
        yield 'negative numeric string' => ['-5', -5];
        yield 'nonnumeric string' => ['x', null];
        yield 'null' => [null, null];
    }

    public function testGetRequiredInt(): void
    {
        self::assertSame(7, Arry::getRequiredInt(['k' => 7], 'k'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getRequiredInt(['k' => 'x'], 'k');
    }

    /**
     * @param class-string<Throwable>|null $expectedException
     */
    #[DataProvider('nonNegativeIntProvider')]
    public function testGetNonNegativeInt(mixed $value, ?int $expected, ?string $expectedException): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }
        $result = Arry::getNonNegativeInt(['k' => $value], 'k');
        if (!$expectedException) {
            self::assertSame($expected, $result);
        }
    }

    /**
     * @return Generator<string, array{0: mixed, 1: ?int, 2: ?class-string}>
     */
    public static function nonNegativeIntProvider(): Generator
    {
        yield 'null' => [null, null, null];
        yield 'zero int' => [0, 0, null];
        yield 'positive int' => [3, 3, null];
        yield 'numeric string zero' => ['0', 0, null];
        yield 'numeric string positive' => ['5', 5, null];
        yield 'leading zero invalid' => ['01', null, InvalidArgumentException::class];
        yield 'negative int invalid' => [-1, null, InvalidArgumentException::class];
        yield 'negative string invalid' => ['-2', null, InvalidArgumentException::class];
        yield 'nonnumeric string invalid' => ['x', null, InvalidArgumentException::class];
    }

    public function testGetRequiredNonNegativeInt(): void
    {
        self::assertSame(2, Arry::getRequiredNonNegativeInt(['k' => 2], 'k'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getRequiredNonNegativeInt([], 'k');
    }

    /**
     * @param class-string<Throwable>|null $expectedException
     */
    #[DataProvider('positiveIntProvider')]
    public function testGetPositiveInt(mixed $value, ?int $expected, ?string $expectedException): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }
        $result = Arry::getPositiveInt(['k' => $value], 'k');
        if (!$expectedException) {
            self::assertSame($expected, $result);
        }
    }

    /**
     * @return Generator<string, array{0: mixed, 1: ?int, 2: ?class-string}>
     */
    public static function positiveIntProvider(): Generator
    {
        yield 'null' => [null, null, null];
        yield 'positive int' => [7, 7, null];
        yield 'positive numeric string' => ['11', 11, null];
        yield 'zero int invalid' => [0, null, InvalidArgumentException::class];
        yield 'zero string invalid' => ['0', null, InvalidArgumentException::class];
        yield 'negative int invalid' => [-1, null, InvalidArgumentException::class];
        yield 'negative string invalid' => ['-3', null, InvalidArgumentException::class];
        yield 'nonnumeric string invalid' => ['foo', null, InvalidArgumentException::class];
    }

    public function testGetRequiredPositiveInt(): void
    {
        self::assertSame(5, Arry::getRequiredPositiveInt(['k' => 5], 'k'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getRequiredPositiveInt([], 'k');
    }

    /**
     * @param class-string<Throwable>|null $expectedException
     */
    #[DataProvider('getFloatProvider')]
    public function testGetFloat(mixed $value, ?float $expected, ?string $expectedException): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }
        $result = Arry::getFloat(['k' => $value], 'k');
        if (!$expectedException) {
            self::assertSame($expected, $result);
        }
    }

    /**
     * @return Generator<string, array{0: mixed, 1: ?float, 2: ?class-string}>
     */
    public static function getFloatProvider(): Generator
    {
        yield 'null' => [null, null, null];
        yield 'float' => [1.25, 1.25, null];
        yield 'int to float' => [3, 3.0, null];
        yield 'numeric string' => ['2.5', 2.5, null];
        yield 'nonnumeric string cast to 0.0' => ['x', 0.0, null];
        yield 'invalid type' => [new \stdClass(), null, InvalidArgumentException::class];
    }

    public function testGetRequiredFloat(): void
    {
        self::assertSame(1.0, Arry::getRequiredFloat(['k' => 1], 'k'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getRequiredFloat([], 'k');
    }

    public function testGetArray(): void
    {
        $data = ['a' => ['b' => [1, 2]]];
        self::assertSame([1, 2], Arry::getArray($data, 'a', 'b'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getArray(['a' => 1], 'a', 'b');
    }

    public function testGetRequiredArray(): void
    {
        self::assertSame([1], Arry::getRequiredArray(['k' => [1]], 'k'));
    }

    public function testGetString(): void
    {
        self::assertSame('42', Arry::getString(['k' => 42], 'k'));
        self::assertNull(Arry::getString(['k' => null], 'k'));
    }

    public function testGetRequiredString(): void
    {
        self::assertSame('a', Arry::getRequiredString(['k' => 'a'], 'k'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getRequiredString([], 'k');
    }

    public function testGetStringMapAndRequiredStringMap(): void
    {
        $data = ['outer' => ['x' => 1, 'y' => 2]];
        self::assertSame(['x' => 1, 'y' => 2], Arry::getStringMap($data, 'outer'));

        $this->expectException(InvalidArgumentException::class);
        Arry::getStringMap(['outer' => [0 => 'a']], 'outer');

        self::assertSame(['x' => 1], Arry::getRequiredStringMap(['m' => ['x' => 1]], 'm'));
    }

    public function testGetMapAndRequiredMap(): void
    {
        $data = ['outer' => ['a' => 1]];
        self::assertSame(['a' => 1], Arry::getMap($data, 'outer'));

        $this->expectException(ValueError::class);
        Arry::getMap(['outer' => [0 => 'v']], 'outer');

        self::assertSame(['k' => 'v'], Arry::getRequiredMap(['o' => ['k' => 'v']], 'o'));
    }

    public function testAsMap(): void
    {
        self::assertSame(['a' => 1], Arry::asMap(['a' => 1]));

        $this->expectException(ValueError::class);
        Arry::asMap([0 => 'v']);
    }

    public function testAsStringMap(): void
    {
        self::assertSame(['a' => 1], Arry::asStringMap(['a' => 1]));

        $this->expectException(\InvalidArgumentException::class);
        Arry::asStringMap([0 => 'v']);
    }

    public function testAssertStringKeyArrayAndAssertIntKeyArray(): void
    {
        self::assertSame(['a' => 1], Arry::assertStringKeyArray(['a' => 1]));
        self::assertSame([0 => 'x', 1 => 'y'], Arry::assertIntKeyArray([0 => 'x', 1 => 'y']));

        $this->expectException(ValueError::class);
        Arry::assertStringKeyArray([0 => 'x']);
    }

    public function testAssertList(): void
    {
        self::assertSame([], Arry::assertList([]));
        self::assertSame(['a', 'b'], Arry::assertList([0 => 'a', 1 => 'b']));

        $this->expectException(MissingArrayKeyException::class);
        Arry::assertList([1 => 'a', 2 => 'b']);
    }

    public function testGetListAndRequiredList(): void
    {
        $data = ['path' => [0 => 'a', 1 => 'b']];
        self::assertSame(['a', 'b'], Arry::getList($data, 'path'));
        self::assertNull(Arry::getList(['path' => null], 'path'));

        self::assertSame(['a'], Arry::getRequiredList(['path' => [0 => 'a']], 'path'));
    }

    public function testGetRequiredListMissing(): void
    {
        $this->expectException(MissingArrayKeyException::class);
        Arry::getRequiredList([], 'path');
    }
}
