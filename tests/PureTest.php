<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Pure;

final class PureTest extends TestCase
{
    private $pure;

    protected function setUp(): void
    {
        $this->pure = new Pure(fn (int ...$xs) => implode(':', $xs));
    }

    public function testProxyUnderlyingCallable(): void
    {
        $test = ($this->pure)(1, 2, 3);

        $this->assertEquals($test, '1:2:3');
    }

    public function testCanBeCurryed(): void
    {
        $test = $this->pure->curry(1)(2, 3);

        $this->assertEquals($test, '1:2:3');
    }
}
