<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class UrlTest extends TestCase
{
    public function testExtendsAbstractString(): void
    {
        $test = new Types\Url('https://example.com');

        $this->assertInstanceOf(Types\AbstractString::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class('https://example.com') extends Types\Url
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithUrlString(): void
    {
        new Types\Url('https://example.com');

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonUrlString(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\Url('test');
    }
}
