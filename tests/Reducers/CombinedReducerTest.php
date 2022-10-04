<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\Reducers\CombinedReducer;
use Quanta\Validation\Reducers\ReducerInterface;

final class CombinedReducerTest extends TestCase
{
    private ReducerInterface $reducer;

    protected function setup(): void
    {
        $this->reducer = $this->createMock(ReducerInterface::class);
    }

    public function testImplementsReducerInterface(): void
    {
        $this->assertInstanceOf(ReducerInterface::class, new CombinedReducer(Validation::factory(), $this->reducer));
    }

    public function testItAppliesTheGivenSuccessToTheGivenFactory(): void
    {
        $result = Result::success('result');
        $parameter = Result::success('parameter');

        $factory = Result::pure(fn () => 1);

        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->once())->method('__invoke')->with(1)->willReturn($parameter);

        $this->reducer->expects($this->once())
            ->method('__invoke')
            ->with($factory, $parameter)
            ->willReturn($result);

        $reducer = new CombinedReducer(Validation::factory()->rule($rule), $this->reducer);

        $test = $reducer($factory, Result::success(1));

        $this->assertSame($test, $result);
    }

    public function testItAppliesTheGivenErrorToTheGivenFactory(): void
    {
        $error1 = Result::error('default1');
        $error2 = Result::error('default2');

        $factory = Result::pure(fn () => 1);

        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->never())->method('__invoke');

        $this->reducer->expects($this->once())
            ->method('__invoke')
            ->with($factory, $error1)
            ->willReturn($error2);

        $reducer = new CombinedReducer(Validation::factory()->rule($rule), $this->reducer);

        $test = $reducer($factory, $error1);

        $this->assertSame($test, $error2);
    }

    public function testItAppliesTheGivenSuccessToTheGivenError(): void
    {
        $error1 = Result::error('default1');
        $error2 = Result::error('default2');
        $parameter = Result::success('parameter');

        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->once())->method('__invoke')->with(1)->willReturn($parameter);

        $this->reducer->expects($this->once())
            ->method('__invoke')
            ->with($error1, $parameter)
            ->willReturn($error2);

        $reducer = new CombinedReducer(Validation::factory()->rule($rule), $this->reducer);

        $test = $reducer($error1, Result::success(1));

        $this->assertSame($test, $error2);
    }

    public function testItAppliesTheGivenErrorToTheGivenError(): void
    {
        $error1 = Result::error('default1');
        $error2 = Result::error('default2');
        $error3 = Result::error('default3');

        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->never())->method('__invoke');

        $this->reducer->expects($this->once())
            ->method('__invoke')
            ->with($error1, $error2)
            ->willReturn($error3);

        $reducer = new CombinedReducer(Validation::factory()->rule($rule), $this->reducer);

        $test = $reducer($error1, $error2);

        $this->assertSame($test, $error3);
    }
}
