<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class WrappedCallableTest extends TestCase
{
    protected function setUp(): void
    {
        $this->callable = $this->createMock(TestCallable::class);

        $this->rule = new Rules\WrappedCallable($this->callable);
    }

    public function testReturnsSuccessWhenCallableDoesNotThrow(): void
    {
        $this->callable->expects($this->once())
            ->method('__invoke')
            ->with('parameter')
            ->willReturn('result');

        $test = ($this->rule)('parameter');

        $this->assertEquals($test, Result::success('result'));
    }

    public function testReturnsErrorWhenCallableThrowsInvalidDataException(): void
    {
        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $this->callable->expects($this->once())
            ->method('__invoke')
            ->with('parameter')
            ->willThrowException(new InvalidDataException($error1, $error2, $error3));

        $test = ($this->rule)('parameter');

        $this->assertEquals($test, Result::errors($error1, $error2, $error3));
    }

    public function testDoNothingWhenCallableThrowsAnyOtherException(): void
    {
        $this->callable->expects($this->once())
            ->method('__invoke')
            ->with('parameter')
            ->willThrowException(new Exception);

        $this->expectException(Exception::class);

        ($this->rule)('parameter');
    }
}
