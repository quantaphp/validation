<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Result;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\Reducers\Reducer;
use Quanta\Validation\Reducers\VariadicReducer;
use Quanta\Validation\Reducers\ReducerInterface;

final class TestClassFactoryTest
{
    public $xs;

    public function __construct(int ...$xs)
    {
        $this->xs = $xs;
    }
}

final class TestAbstractInputFactoryTest extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory;
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

    public function testValidationReturnsTheSameInstanceWhenNoParameterIsGiven(): void
    {
        $factory = Factory::from(fn () => 1);

        $test = $factory->validation();

        $this->assertSame($test, $factory);
    }

    public function testValidationReturnsANewInstanceWhenReducersAreGiven(): void
    {
        $factory = Factory::from(fn () => 1);

        $reducer1 = $this->createMock(ReducerInterface::class);
        $reducer2 = $this->createMock(ReducerInterface::class);
        $reducer3 = $this->createMock(ReducerInterface::class);

        $test = $factory->validation($reducer1, $reducer2, $reducer3);

        $this->assertNotSame($test, $factory);
        $this->assertInstanceOf(Factory::class, $test);
    }

    public function testValidationWrapsTheGivenValidationsIntoReducers(): void
    {
        $factory = Factory::from(fn () => 1);

        $validation1 = Validation::factory();
        $validation2 = Validation::factory();
        $validation3 = Validation::factory();

        $test = $factory->validation($validation1, $validation2, $validation3);

        $this->assertNotSame($test, $factory);
        $this->assertInstanceOf(Factory::class, $test);
        $this->assertEquals($test, $factory->validation(
            new Reducer($validation1),
            new Reducer($validation2),
            new Reducer($validation3),
        ));
    }

    public function testVariadicWrapsTheGivenAbstractInputClassNameIntoVariadicReducer(): void
    {
        $f = fn () => 1;

        $factory = Factory::from($f);

        $test = $factory->variadic(TestAbstractInputFactoryTest::class);

        $this->assertNotSame($test, $factory);
        $this->assertInstanceOf(Factory::class, $test);
        $this->assertEquals($test, $factory->validation(VariadicReducer::from(TestAbstractInputFactoryTest::class)));
    }

    public function testVariadicWrapsTheGivenReducerIntoVariadicReducer(): void
    {
        $f = fn () => 1;

        $factory = Factory::from($f);

        $reducer = $this->createMock(ReducerInterface::class);

        $test = $factory->variadic($reducer);

        $this->assertNotSame($test, $factory);
        $this->assertInstanceOf(Factory::class, $test);
        $this->assertEquals($test, $factory->validation(VariadicReducer::from($reducer)));
    }

    public function testVariadicWrapsTheGivenValidationIntoVariadicReducer(): void
    {
        $f = fn () => 1;

        $factory = Factory::from($f);

        $validation = Validation::factory();

        $test = $factory->variadic($validation);

        $this->assertNotSame($test, $factory);
        $this->assertInstanceOf(Factory::class, $test);
        $this->assertEquals($test, $factory->validation(VariadicReducer::from($validation)));
    }

    public function testItCallsTheFactoryCallableWithNoParameterWhenThereIsNoReducer(): void
    {
        $callable = $this->createMock(TestCallable::class);

        $callable->expects($this->once())->method('__invoke')->willReturn('result');

        $factory = Factory::from($callable);

        $test = $factory([1, 2, 3]);

        $this->assertEquals($test, 'result');
    }

    public function testItAppiesReducersOnTheFactoryCallable(): void
    {
        $input = [1, 2, 3];

        $callable = fn () => 1;

        $pure1 = Result::pure(fn () => 'partial');
        $pure2 = Result::pure(fn () => 'partial');
        $pure3 = Result::pure(fn () => 'result');

        $reducer1 = $this->createMock(ReducerInterface::class);
        $reducer2 = $this->createMock(ReducerInterface::class);
        $reducer3 = $this->createMock(ReducerInterface::class);

        $reducer1->expects($this->once())->method('__invoke')
            ->with(Result::pure($callable), Result::unit($input))
            ->willReturn($pure1);

        $reducer2->expects($this->once())->method('__invoke')
            ->with($pure1, Result::unit($input))
            ->willReturn($pure2);

        $reducer3->expects($this->once())->method('__invoke')
            ->with($pure2, Result::unit($input))
            ->willReturn($pure3);

        $factory = Factory::from($callable)->validation($reducer1, $reducer2, $reducer3);

        $test = $factory($input);

        $this->assertEquals($test, 'result');
    }
}
