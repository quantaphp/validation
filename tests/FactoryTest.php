<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\VariadicValidation;
use Quanta\ValidationInterface;
use Quanta\Validation\Result;
use Quanta\Validation\Factory;

final class TestClassFactoryTest
{
    public $xs;

    public function __construct(int ...$xs)
    {
        $this->xs = $xs;
    }
}

final class FactoryTest extends TestCase
{
    public function testFromReturnsAnInstanceOfFactory(): void
    {
        $this->assertInstanceOf(Factory::class, Factory::from(fn () => 1));
    }

    public function testClassReturnsAnInstanceOfFactory(): void
    {
        $this->assertInstanceOf(Factory::class, Factory::class(TestClassFactoryTest::class));
    }

    public function testValidationReturnsTheSameInstanceWhenNoValidationAreGiven(): void
    {
        $factory = Factory::from(fn () => 1);

        $test = $factory->validation();

        $this->assertSame($test, $factory);
    }

    public function testValidationReturnsANewInstanceWhenValidationsAreGiven(): void
    {
        $factory = Factory::from(fn () => 1);

        $validation1 = $this->createMock(ValidationInterface::class);
        $validation2 = $this->createMock(ValidationInterface::class);
        $validation3 = $this->createMock(ValidationInterface::class);

        $test = $factory->validation($validation1, $validation2, $validation3);

        $this->assertNotSame($test, $factory);
        $this->assertInstanceOf(Factory::class, $test);
    }

    public function testVariadicWrapsTheGivenValidationIntoVariadicValidation(): void
    {
        $f = fn () => 1;

        $factory = Factory::from($f);

        $validation = $this->createMock(ValidationInterface::class);

        $test = $factory->variadic($validation);

        $this->assertEquals($test, $factory->validation(VariadicValidation::from($validation)));
    }

    public function testItAppiesValidationOnTheFactoryCallable(): void
    {
        $v = Validation::factory();

        $factory = Factory::from(fn (int ...$xs) => implode(':', $xs));

        $factory = $factory->validation($v->rule(fn (array $data) => Result::success($data[0])));
        $factory = $factory->validation($v->rule(fn (array $data) => Result::success($data[1])));
        $factory = $factory->validation($v->rule(fn (array $data) => Result::success($data[2])));

        $test = $factory([1, 2, 3]);

        $this->assertEquals($test, '1:2:3');
    }

    public function testItAppiesValidationOnTheFactoryClassConstructor(): void
    {
        $v = Validation::factory();

        $factory = Factory::class(TestClassFactoryTest::class);

        $factory = $factory->validation($v->rule(fn (array $data) => Result::success($data[0])));
        $factory = $factory->validation($v->rule(fn (array $data) => Result::success($data[1])));
        $factory = $factory->validation($v->rule(fn (array $data) => Result::success($data[2])));

        $test = $factory([1, 2, 3]);

        $this->assertEquals($test, new TestClassFactoryTest(1, 2, 3));
    }
}
