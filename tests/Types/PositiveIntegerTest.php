<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class PositiveIntegerTest extends TestCase
{
    public function testExtendsAbstractInteger(): void
    {
        $test = new Types\PositiveInteger(1);

        $this->assertInstanceOf(Types\AbstractInteger::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class(1) extends Types\PositiveInteger
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithPositiveInteger(): void
    {
        new Types\PositiveInteger(1);

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithZero(): void
    {
        new Types\PositiveInteger(0);

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonPositiveInteger(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\PositiveInteger(-1);
    }

    public function testValueReturnsValue(): void
    {
        $value = new Types\PositiveInteger(1);

        $test = $value->value();

        $this->assertEquals($test, 1);
    }

    public function testCanBeCastedAsString(): void
    {
        $value = new Types\PositiveInteger(1);

        $test = (string) $value;

        $this->assertEquals($test, '1');
    }

    public function testCanBeEncodedAsJson(): void
    {
        $value = new Types\PositiveInteger(1);

        $test = json_encode($value);

        $this->assertEquals($test, '1');
    }
}
