<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class StrictlyPositiveIntegerTest extends TestCase
{
    public function testExtendsAbstractInteger(): void
    {
        $test = new Types\StrictlyPositiveInteger(1);

        $this->assertInstanceOf(Types\AbstractInteger::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class(1) extends Types\StrictlyPositiveInteger
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithStrictlyPositiveInteger(): void
    {
        new Types\StrictlyPositiveInteger(1);

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithZero(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\StrictlyPositiveInteger(0);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonStrictlyPositiveInteger(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\StrictlyPositiveInteger(-1);
    }

    public function testValueReturnsValue(): void
    {
        $value = new Types\StrictlyPositiveInteger(1);

        $test = $value->value();

        $this->assertEquals($test, 1);
    }

    public function testCanBeCastedAsString(): void
    {
        $value = new Types\StrictlyPositiveInteger(1);

        $test = (string) $value;

        $this->assertEquals($test, '1');
    }

    public function testCanBeEncodedAsJson(): void
    {
        $value = new Types\StrictlyPositiveInteger(1);

        $test = json_encode($value);

        $this->assertEquals($test, '1');
    }
}
