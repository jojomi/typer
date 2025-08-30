<?php

declare(strict_types=1);

namespace Jojomi\Typer\Tests;

use Generator;
use Jojomi\Typer\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Type::class)]
class TypeTest extends TestCase
{
    #[DataProvider('printProvider')]
    public function testPrint(mixed $input, string $expected): void
    {
        self::assertSame($expected, Type::print($input));
    }

    /**
     * @return Generator<string, array{0: mixed, 1: string}>
     */
    public static function printProvider(): Generator
    {
        yield 'null' => [null, 'null'];
        yield 'int' => [123, 'int'];
        yield 'float' => [1.23, 'float'];
        yield 'string' => ['abc', 'string'];
        yield 'bool true' => [true, 'bool'];
        yield 'bool false' => [false, 'bool'];
        yield 'array' => [[1, 2], 'array'];

        $obj = new stdClass();
        yield 'object fqcn' => [$obj, stdClass::class];
    }

    public function testPrintResource(): void
    {
        $handle = fopen('php://memory', 'r');
        try {
            self::assertSame('resource', Type::print($handle));
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }
}