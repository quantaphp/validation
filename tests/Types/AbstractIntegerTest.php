<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;

final class AbstractIntegerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->value = new class(1) extends Types\AbstractInteger
        {
        };
    }

    public function testValueReturnsValue(): void
    {
        $test = $this->value->value();

        $this->assertEquals($test, 1);
    }

    public function testCanBeCastedAsString(): void
    {
        $test = (string) $this->value;

        $this->assertEquals($test, '1');
    }

    public function testCanBeEncodedAsJson(): void
    {
        $test = json_encode($this->value);

        $this->assertEquals($test, '1');
    }
}
