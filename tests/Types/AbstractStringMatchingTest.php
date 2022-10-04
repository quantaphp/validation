<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class AbstractStringMatchingTest extends TestCase
{
    public function testExtendsAbstractString(): void
    {
        $test = new class('value') extends Types\AbstractStringMatching
        {
            protected static function pattern(): string
            {
                return '/value/';
            }
        };

        $this->assertInstanceOf(Types\AbstractString::class, $test);
    }

    public function testCanBeInstantiatedWithStringMatchingThePattern(): void
    {
        new class('1.2.3.4.5.') extends Types\AbstractStringMatching
        {
            protected static function pattern(): string
            {
                return '/([0-9]\.)+/';
            }
        };

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithStringNotMatchingThePattern(): void
    {
        $this->expectException(InvalidDataException::class);

        new class('value') extends Types\AbstractStringMatching
        {
            protected static function pattern(): string
            {
                return '/([0-9]\.)+/';
            }
        };
    }
}
