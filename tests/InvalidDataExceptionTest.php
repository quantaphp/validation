<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\ErrorFormatter;
use Quanta\Validation\ErrorFormatterInterface;
use Quanta\Validation\InvalidDataException;

final class InvalidDataExceptionTest extends TestCase
{
    public function testestImplementsThrowable(): void
    {
        $this->assertInstanceOf(Throwable::class, new InvalidDataException(Error::from('default')));
    }

    public function testResultReturnsAnErrorResultWithTheErrors(): void
    {
        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $exception = new InvalidDataException($error1, $error2, $error3);

        $test = $exception->result();

        $this->assertEquals($test, Result::errors($error1, $error2, $error3));
    }

    public function testUsesDefaultErrorFormatterWhenNoErrorFormatterGiven(): void
    {
        $params = ['key1' => 'a', 'key2' => 'b', 'key3' => 1];

        $error1 = (Error::from('> {key} %s:%s:%s <', $params));
        $error2 = (Error::from('> {key} %s:%s:%s <', $params)->nested('key1'));
        $error3 = (Error::from('> {key} %s:%s:%s <', $params)->nested('key1', 'key2', 'key3'));
        $error4 = (Error::from('> %s:%s:%s <', $params)->nested('key1', 'key2', 'key3'));

        $formatter = new ErrorFormatter;

        $exception = new InvalidDataException($error1, $error2, $error3, $error4);

        [$test1, $test2, $test3, $test4] = $exception->messages();

        $this->assertEquals($test1, $formatter($error1));
        $this->assertEquals($test2, $formatter($error2));
        $this->assertEquals($test3, $formatter($error3));
        $this->assertEquals($test4, $formatter($error4));
    }

    public function testUsesTheGivenErrorFormatter(): void
    {
        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $formatter = $this->createMock(ErrorFormatterInterface::class);

        $formatter->expects($this->exactly(3))->method('__invoke')->willReturnMap([
            [$error1, 'error1'],
            [$error2, 'error2'],
            [$error3, 'error3'],
        ]);

        $exception = new InvalidDataException($error1, $error2, $error3);

        [$test1, $test2, $test3] = $exception->messages($formatter);

        $this->assertEquals($test1, 'error1');
        $this->assertEquals($test2, 'error2');
        $this->assertEquals($test3, 'error3');
    }
}
