<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\Reducers\Reducer;
use Quanta\Validation\Reducers\ReducerInterface;

final class ReducerTest extends TestCase
{
    public function testImplementsReducerInterface(): void
    {
        $this->assertInstanceOf(ReducerInterface::class, new Reducer(Validation::factory()));
    }

    public function testItAppliesTheGivenSuccessToTheGivenFactory(): void
    {
        $rule = $this->createMock(TestCallable::class);
        $factory = $this->createMock(TestCallable::class);

        $rule->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success('parameter'));
        $factory->expects($this->once())->method('__invoke')->with('parameter')->willReturn('result');

        $reducer = new Reducer(Validation::factory()->rule($rule));

        $test = $reducer(Result::pure($factory), Result::success(1))->value();

        $this->assertEquals($test, 'result');
    }

    public function testItAppliesTheGivenErrorToTheGivenFactory(): void
    {
        $error = Result::error('default');

        $rule = $this->createMock(TestCallable::class);
        $factory = $this->createMock(TestCallable::class);

        $rule->expects($this->never())->method('__invoke');
        $factory->expects($this->never())->method('__invoke');

        $reducer = new Reducer(Validation::factory()->rule($rule));

        $test = $reducer(Result::pure($factory), $error);

        $this->assertSame($test, $error);
    }

    public function testItAppliesTheGivenSuccessToTheGivenError(): void
    {
        $error = Result::error('default');

        $rule = $this->createMock(TestCallable::class);
        $factory = $this->createMock(TestCallable::class);

        $rule->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success('parameter'));
        $factory->expects($this->never())->method('__invoke');

        $reducer = new Reducer(Validation::factory()->rule($rule));

        $test = $reducer($error, Result::success(1));

        $this->assertSame($test, $error);
    }

    public function testItAppliesTheGivenErrorToTheGivenError(): void
    {
        $error1 = Error::from('default1');
        $error2 = Error::from('default2');

        $rule = $this->createMock(TestCallable::class);
        $factory = $this->createMock(TestCallable::class);

        $rule->expects($this->never())->method('__invoke');
        $factory->expects($this->never())->method('__invoke');

        $reducer = new Reducer(Validation::factory()->rule($rule));

        $test = $reducer(Result::errors($error1), Result::errors($error2));

        $this->assertEquals($test, Result::errors($error1, $error2));
    }
}
