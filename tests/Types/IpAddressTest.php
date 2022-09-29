<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;
use Quanta\Validation\InvalidDataException;

final class IpAddressTest extends TestCase
{
    public function testExtendsAbstractString(): void
    {
        $test = new Types\IpAddress('127.0.0.1');

        $this->assertInstanceOf(Types\AbstractString::class, $test);
    }

    public function testCanBeExtended(): void
    {
        new class('127.0.0.1') extends Types\IpAddress
        {
        };

        $this->assertTrue(true);
    }

    public function testCanBeInstanciatedWithIpAddressString(): void
    {
        new Types\IpAddress('127.0.0.1');

        $this->assertTrue(true);
    }

    public function testThrowsInvalidDataExceptionWhenInstanciatedWithNonIpAddressString(): void
    {
        $this->expectException(InvalidDataException::class);

        new Types\IpAddress('test');
    }
}
