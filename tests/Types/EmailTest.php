<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class EmailTest extends TestCase
{
    public function testExtendsAbstractString(): void
    {
        $test = new Types\Email('test@example.com');

        $this->assertInstanceOf(Types\AbstractString::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class('test@example.com') extends Types\Email
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithEmailString(): void
    {
        new Types\Email('test@example.com');

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonEmailString(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\Email('test');
    }
}
