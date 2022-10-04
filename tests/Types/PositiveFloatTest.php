<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class PositiveFloatTest extends TestCase
{
    public function testExtendsAbstractFloat(): void
    {
        $test = new Types\PositiveFloat(0.1);

        $this->assertInstanceOf(Types\AbstractFloat::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class(1) extends Types\PositiveFloat
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithPositiveFloat(): void
    {
        new Types\PositiveFloat(0.1);

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithZero(): void
    {
        new Types\PositiveFloat(0.0);

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonPositiveFloat(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\PositiveFloat(-0.1);
    }
}
