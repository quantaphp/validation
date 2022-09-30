<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\ValidationInterface;
use Quanta\Validation\Pure;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class ResultTest extends TestCase
{
    public function testUnitAliasesSuccess(): void
    {
        $this->assertEquals(Result::unit(1), Result::success(1));
    }

    public function testPureAliasesSuccessWithWrappedFunction(): void
    {
        $f = fn () => 1;

        $this->assertEquals(Result::pure($f), Result::success(new Pure($f)));
    }

    public function testErrorAliasesErrors(): void
    {
        $this->assertEquals(
            Result::error('default', ['p1', 'p2', 'p3'], 'l1', 'l2', 'l3'),
            Result::errors(Error::from('default', ['p1', 'p2', 'p3'], 'l1', 'l2', 'l3'))
        );
    }

    public function testErrorHasDefaultParamsAndLabels(): void
    {
        $this->assertEquals(Result::error('default'), Result::errors(Error::from('default')));
    }

    public function testSuccessfulResultReturnsValue(): void
    {
        $result1 = Result::success('value');
        $result2 = Result::success('value', true);
        $result3 = Result::success('value', false, 'key1', 'key2', 'key3');
        $result4 = Result::success('value', true, 'key1', 'key2', 'key3');

        $test1 = $result1->value();
        $test2 = $result2->value();
        $test3 = $result3->value();
        $test4 = $result4->value();

        $this->assertEquals($test1, 'value');
        $this->assertEquals($test2, 'value');
        $this->assertEquals($test3, 'value');
        $this->assertEquals($test4, 'value');
    }

    public function testErrorResultThrowsInvalidDataException(): void
    {
        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $result = Result::errors($error1, $error2, $error3);

        try {
            $result->value();
        } catch (InvalidDataException $e) {
            $this->assertEquals($e, new InvalidDataException($error1, $error2, $error3));
        }
    }

    public function testLiftnWorks(): void
    {
        $factory = $this->createMock(TestCallable::class);

        $factory->expects($this->once())->method('__invoke')->with(1, 2, 3)->willReturn('result');

        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $lifted = Result::liftn($factory);

        $test1 = $lifted(Result::success(1), Result::success(2), Result::success(3))->value();
        $test2 = $lifted(Result::success(1), Result::errors($error1), Result::success(3), Result::errors($error2, $error3));

        $this->assertEquals($test1, 'result');
        $this->assertEquals($test2, Result::errors($error1, $error2, $error3));
    }

    public function testApplyWorksForPure(): void
    {
        $factory = $this->createMock(TestCallable::class);

        $factory->expects($this->once())->method('__invoke')->with(1, 2, 3)->willReturn('result');

        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $pure = Result::pure($factory);

        $pure1 = Result::apply($pure)(Result::success(1));
        $pure1 = Result::apply($pure1)(Result::success(2));
        $pure1 = Result::apply($pure1)(Result::success(3));

        $pure2 = Result::apply($pure)(Result::success(1));
        $pure2 = Result::apply($pure2)(Result::errors($error1));
        $pure2 = Result::apply($pure2)(Result::success(3));
        $pure2 = Result::apply($pure2)(Result::errors($error2, $error3));

        $test1 = $pure1->value();
        $test2 = $pure2;

        $this->assertEquals($test1, 'result');
        $this->assertEquals($test2, Result::errors($error1, $error2, $error3));
    }

    public function testApplyWorksForError(): void
    {
        $factory = $this->createMock(TestCallable::class);

        $factory->expects($this->never())->method('__invoke');

        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $pure = Result::errors($error1);

        $pure1 = Result::apply($pure)(Result::success(1));
        $pure1 = Result::apply($pure1)(Result::success(2));
        $pure1 = Result::apply($pure1)(Result::success(3));

        $pure2 = Result::apply($pure)(Result::errors($error2));
        $pure2 = Result::apply($pure2)(Result::success(2));
        $pure2 = Result::apply($pure2)(Result::errors($error3));

        $test1 = $pure1;
        $test2 = $pure2;

        $this->assertEquals($test1, Result::errors($error1));
        $this->assertEquals($test2, Result::errors($error1, $error2, $error3));
    }

    public function testApplyThrowsForSuccessNotContainingAnInstanceOfPure(): void
    {
        $this->expectException(UnexpectedValueException::class);

        Result::apply(Result::success(1));
    }

    public function testBindWorksAsExpectedForFunctionReturningSuccess(): void
    {
        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->exactly(2))
            ->method('__invoke')
            ->with(1)
            ->willReturn(Result::success('result'));

        $f = Result::bind($rule);

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));

        $this->assertEquals($test1, Result::success('result'));
        $this->assertEquals($test2, Result::success('result', false, 'a1', 'a2', 'a3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));
    }

    public function testBindWorksAsExpectedForFunctionReturningNestedSuccess(): void
    {
        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->exactly(2))
            ->method('__invoke')
            ->with(1)
            ->willReturn(Result::success('result', false, 'b1', 'b2', 'b3'));

        $f = Result::bind($rule);

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));

        $this->assertEquals($test1, Result::success('result', false, 'b1', 'b2', 'b3'));
        $this->assertEquals($test2, Result::success('result', false, 'a1', 'a2', 'a3', 'b1', 'b2', 'b3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));
    }

    public function testBindWorksAsExpectedForFunctionReturningDefaultSuccess(): void
    {
        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->exactly(2))
            ->method('__invoke')
            ->with(1)
            ->willReturn(Result::success('result', true));

        $f = Result::bind($rule);

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));

        $this->assertEquals($test1, Result::success('result', true));
        $this->assertEquals($test2, Result::success('result', true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));
    }

    public function testBindWorksAsExpectedForFunctionReturningNestedDefaultSuccess(): void
    {
        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->exactly(2))
            ->method('__invoke')
            ->with(1)
            ->willReturn(Result::success('result', true, 'b1', 'b2', 'b3'));

        $f = Result::bind($rule);

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));

        $this->assertEquals($test1, Result::success('result', true, 'b1', 'b2', 'b3'));
        $this->assertEquals($test2, Result::success('result', true, 'a1', 'a2', 'a3', 'b1', 'b2', 'b3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));
    }

    public function testBindWorksAsExpectedForFunctionReturningError(): void
    {
        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->exactly(2))
            ->method('__invoke')
            ->with(1)
            ->willReturn(Result::errors(Error::from('default', ['q1', 'q2', 'q3'])->nested('c1', 'c2', 'c3')));

        $f = Result::bind($rule);

        $test1 = $f(Result::success(1));
        $test3 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test2 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));

        $this->assertEquals($test1, Result::errors(Error::from('default', ['q1', 'q2', 'q3'])->nested('c1', 'c2', 'c3')));
        $this->assertEquals($test3, Result::errors(Error::from('default', ['q1', 'q2', 'q3'])->nested('a1', 'a2', 'a3', 'c1', 'c2', 'c3')));
        $this->assertEquals($test2, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::errors(Error::from('default', ['p1', 'p2', 'p3'])->nested('e1', 'e2', 'e3')));
    }

    public function testBindThrowsForCallablesNotReturningAResult(): void
    {
        $rule = $this->createMock(TestCallable::class);

        $rule->expects($this->exactly(1))
            ->method('__invoke')
            ->with(1)
            ->willReturn(2);

        $f = Result::bind($rule);

        $this->expectException(UnexpectedValueException::class);

        $f(Result::success(1));
    }

    public function variadicProvider(): Traversable
    {
        $iterables = [
            'array' => [1, 2, 3],
            'iterator' => new ArrayIterator([1, 2, 3]),
            'iterator aggregate' => new class implements IteratorAggregate
            {
                public function getIterator(): Traversable
                {
                    return new ArrayIterator([1, 2, 3]);
                }
            },
        ];

        foreach ($iterables as $key => $iterable) {
            yield $key . ' success' => [Result::success($iterable), [
                Result::success(1, false, '0'),
                Result::success(2, false, '1'),
                Result::success(3, false, '2'),
            ]];

            yield $key . ' nested success' => [Result::success($iterable, false, 'key1', 'key2', 'key3'), [
                Result::success(1, false, 'key1', 'key2', 'key3', '0'),
                Result::success(2, false, 'key1', 'key2', 'key3', '1'),
                Result::success(3, false, 'key1', 'key2', 'key3', '2'),
            ]];

            yield $key . ' default success' => [Result::success($iterable, true), [
                Result::success(1, true, '0'),
                Result::success(2, true, '1'),
                Result::success(3, true, '2'),
            ]];

            yield $key . ' default nested success' => [Result::success($iterable, true, 'key1', 'key2', 'key3'), [
                Result::success(1, true, 'key1', 'key2', 'key3', '0'),
                Result::success(2, true, 'key1', 'key2', 'key3', '1'),
                Result::success(3, true, 'key1', 'key2', 'key3', '2'),
            ]];
        }
    }

    /**
     * @dataProvider variadicProvider
     */
    public function testVariadicWorksAsExpected(Result $init, array $results): void
    {
        $factories = [
            Result::pure(fn () => 1),
            Result::pure(fn () => 2),
            Result::pure(fn () => 3),
            Result::pure(fn () => 4),
        ];

        $validation = $this->createMock(ValidationInterface::class);

        $validation->expects($this->exactly(3))
            ->method('__invoke')
            ->willReturnCallback(function ($factory, $result) use ($factories, $results) {
                if ($factory === $factories[0] && $result == $results[0]) return $factories[1];
                if ($factory === $factories[1] && $result == $results[1]) return $factories[2];
                if ($factory === $factories[2] && $result == $results[2]) return $factories[3];
            });

        $variadic = Result::variadic($validation);

        $test = $variadic($factories[0], $init);

        $this->assertSame($test, $factories[3]);
    }

    public function testVariadicWorksAsExpectedForError(): void
    {
        $factory = Result::pure(fn () => 1);
        $error = Result::error('default1');

        $expected = Result::error('default2');

        $validation = $this->createMock(ValidationInterface::class);

        $validation->expects($this->exactly(1))
            ->method('__invoke')
            ->with($factory, $error)
            ->willReturn($expected);

        $variadic = Result::variadic($validation);

        $test = $variadic($factory, $error);

        $this->assertSame($test, $expected);
    }

    public function testFunctionReturnedByVariadicThrowsForSuccessContainingANonIterableValue(): void
    {
        $validation = $this->createMock(ValidationInterface::class);

        $validation->expects($this->never())->method('__invoke');

        $variadic = Result::variadic($validation);

        $this->expectException(UnexpectedValueException::class);

        $variadic(Result::pure(fn () => 1), Result::success(1));
    }
}
