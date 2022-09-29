<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class NonEmptyStringTest extends TestCase
{
    public function testExtendsAbstractString(): void
    {
        $test = new Types\NonEmptyString('value');

        $this->assertInstanceOf(Types\AbstractString::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class('value') extends Types\NonEmptyString
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithNonEmptyString(): void
    {
        new Types\NonEmptyString('value');

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithEmptyString(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\NonEmptyString('');
    }
}
