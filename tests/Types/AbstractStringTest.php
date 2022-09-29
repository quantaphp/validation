<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Types;

final class AbstractStringTest extends TestCase
{
    protected function setUp(): void
    {
        $this->value = new class('value') extends Types\AbstractString
        {
        };
    }

    public function testValueReturnsValue(): void
    {
        $test = $this->value->value();

        $this->assertEquals($test, 'value');
    }

    public function testCanBeCastedAsString(): void
    {
        $test = (string) $this->value;

        $this->assertEquals($test, 'value');
    }

    public function testCanBeEncodedAsJson(): void
    {
        $test = json_encode($this->value);

        $this->assertEquals($test, '"value"');
    }
}
