<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class StrictlyPositiveFloatTest extends TestCase
{
    public function testExtendsAbstractFloat(): void
    {
        $test = new Types\StrictlyPositiveFloat(1);

        $this->assertInstanceOf(Types\AbstractFloat::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class(1) extends Types\StrictlyPositiveFloat
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithStrictlyPositiveFloat(): void
    {
        new Types\StrictlyPositiveFloat(0.1);

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithZero(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\StrictlyPositiveFloat(0.0);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonStrictlyPositiveFloat(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\StrictlyPositiveFloat(-0.1);
    }
}
