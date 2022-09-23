<?php

declare(strict_types=1);

require_once __DIR__ . '/TestErrorFormatter.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Error;
use Quanta\Validation\ErrorFormatter;
use Quanta\Validation\InvalidDataException;

final class InvalidDataExceptionTest extends TestCase
{
    public function testestImplementsThrowable(): void
    {
        $this->assertInstanceOf(Throwable::class, new InvalidDataException(
            new Error('label', 'default')
        ));
    }

    public function testUsesDefaultErrorFormatterWhenNoErrorFormatterGiven(): void
    {
        $params = ['key1' => 'a', 'key2' => 'b', 'key3' => 1];

        $error1 = (new Error('label', '> {key} %s:%s:%s <', $params));
        $error2 = (new Error('label', '> {key} %s:%s:%s <', $params, 'key1'));
        $error3 = (new Error('label', '> {key} %s:%s:%s <', $params, 'key1', 'key2', 'key3'));
        $error4 = (new Error('label', '> %s:%s:%s <', $params, 'key1', 'key2', 'key3'));

        $formatter = new ErrorFormatter;

        $exception = new InvalidDataException($error1, $error2, $error3, $error4);

        [$test1, $test2, $test3, $test4] = $exception->messages();

        $this->assertEquals($test1, $formatter($error1));
        $this->assertEquals($test2, $formatter($error2));
        $this->assertEquals($test3, $formatter($error3));
        $this->assertEquals($test4, $formatter($error4));
    }

    public function testUsesGivenErrorFormatter(): void
    {
        $error1 = new Error('label1', 'default1', ['a1', 'b1', 'c1'], 'key11', 'key12', 'key13');
        $error2 = new Error('label2', 'default2', ['a2', 'b2', 'c2'], 'key21', 'key22', 'key23');

        $formatter = new TestErrorFormatter;

        $exception = new InvalidDataException($error1, $error2);

        [$test1, $test2] = $exception->messages($formatter);

        $this->assertEquals($test1, $formatter($error1));
        $this->assertEquals($test2, $formatter($error2));
    }
}
