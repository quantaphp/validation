<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Error;
use Quanta\Validation\Rules;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class WrappedTest extends TestCase
{
    protected function setUp(): void
    {
        $this->factory = function (int $i) {
            if ($i == 0) {
                throw new Exception;
            }

            if ($i < 0) {
                throw new InvalidDataException(Error::from('default'));
            }

            return $i * 2;
        };
    }

    public function testReturnsSuccessWhenCallableDoesNotThrow(): void
    {
        $test = (new Rules\Wrapped($this->factory))(1);

        $this->assertEquals($test, Result::success(2));
    }

    public function testReturnsErrorWhenCallableThrowsInvalidDataException(): void
    {
        $test = (new Rules\Wrapped($this->factory))(-1);

        $this->expectException(InvalidDataException::class);

        $test->value();
    }

    public function testDoNothingWhenCallableThrowsAnyOtherException(): void
    {
        $this->expectException(Exception::class);

        (new Rules\Wrapped($this->factory))(0);
    }
}
