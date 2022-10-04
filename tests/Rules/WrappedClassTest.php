<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class TestClassSuccessWrappedClassTest
{
    public function __construct(private string $parameter)
    {
    }
}

final class TestClassErrorWrappedClassTest
{
    public function __construct(private string $parameter)
    {
        throw new InvalidDataException(
            Error::from('default1'),
            Error::from('default2'),
            Error::from('default3'),
        );
    }
}

final class TestClassThrowingWrappedClassTest
{
    public function __construct(private string $parameter)
    {
        throw new Exception;
    }
}

final class WrappedClassTest extends TestCase
{
    public function testReturnsSuccessWhenConstructorDoesNotThrow(): void
    {
        $rule = new Rules\WrappedClass(TestClassSuccessWrappedClassTest::class);

        $test = $rule('parameter');

        $this->assertEquals($test, Result::success(new TestClassSuccessWrappedClassTest('parameter')));
    }

    public function testReturnsErrorWhenConstructorThrowsInvalidDataException(): void
    {
        $rule = new Rules\WrappedClass(TestClassErrorWrappedClassTest::class);

        $test = $rule('parameter');

        $this->assertEquals($test, Result::errors(
            Error::from('default1'),
            Error::from('default2'),
            Error::from('default3'),
        ));
    }

    public function testDoNothingWhenConstructorThrowsAnyOtherException(): void
    {
        $this->expectException(Exception::class);

        $rule = new Rules\WrappedClass(TestClassThrowingWrappedClassTest::class);

        $rule('parameter');
    }
}
