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
    /**
     * @param array<mixed> $data
     */
    #[DataProvider('mapDataProvider')]
    public function testAssertMap(array $data, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValueError::class);
        } else {
            $this->expectNotToPerformAssertions();
        }

        Arry::assertMap($data);
    }

    /**
     * @return Generator<string, array{0: array<mixed>, 1: bool}>
     */
    public static function mapDataProvider(): Generator
    {
        yield 'valid array' => [['key1' => 'value1', 'key2' => 2], true];
        yield 'invalid array' => [['key1' => 'value1', 2 => 'value2'], false];
        yield 'empty array' => [[], true];
    }
}
