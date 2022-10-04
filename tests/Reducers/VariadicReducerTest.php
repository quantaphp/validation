<?php

declare(strict_types=1);

require_once __DIR__ . '/../TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Result;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\Reducers\Reducer;
use Quanta\Validation\Reducers\VariadicReducer;
use Quanta\Validation\Reducers\ReducerInterface;

final class TestAbstractInputVariadicReducerTest extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory;
    }
}

final class VariadicReducerTest extends TestCase
{
    public function testImplementsReducerInterface(): void
    {
        $this->assertInstanceOf(ReducerInterface::class, VariadicReducer::from(new Reducer(Validation::factory())));
    }

    public function testCanBeInstantiatedWithTheNameOfAnAbstractInputImplementation(): void
    {
        $test = VariadicReducer::from(TestAbstractInputVariadicReducerTest::class);

        $this->assertEquals($test, VariadicReducer::from(
            new Reducer(Validation::factory()->rule(TestAbstractInputVariadicReducerTest::class)),
        ));
    }

    public function testCanBeInstantiatedWithAValidation(): void
    {
        $validation = Validation::factory();

        $test = VariadicReducer::from($validation);

        $this->assertEquals($test, VariadicReducer::from(new Reducer($validation)));
    }

    public function testFromThrowsWithString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        VariadicReducer::from('nonclassname');
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

        $reducer = $this->createMock(ReducerInterface::class);

        $reducer->expects($this->exactly(3))
            ->method('__invoke')
            ->willReturnCallback(function ($factory, $result) use ($factories, $results) {
                if ($factory === $factories[0] && $result == $results[0]) return $factories[1];
                if ($factory === $factories[1] && $result == $results[1]) return $factories[2];
                if ($factory === $factories[2] && $result == $results[2]) return $factories[3];
            });

        $reducer = VariadicReducer::from($reducer);

        $test = $reducer($factories[0], $init);

        $this->assertSame($test, $factories[3]);
    }

    public function testVariadicWorksAsExpectedForError(): void
    {
        $factory = Result::pure(fn () => 1);
        $error = Result::error('default1');

        $expected = Result::error('default2');

        $reducer = $this->createMock(ReducerInterface::class);

        $reducer->expects($this->exactly(1))
            ->method('__invoke')
            ->with($factory, $error)
            ->willReturn($expected);

        $reducer = VariadicReducer::from($reducer);

        $test = $reducer($factory, $error);

        $this->assertSame($test, $expected);
    }

    public function testFunctionReturnedByVariadicThrowsForSuccessContainingANonIterableValue(): void
    {
        $reducer = $this->createMock(ReducerInterface::class);

        $reducer->expects($this->never())->method('__invoke');

        $reducer = VariadicReducer::from($reducer);

        $this->expectException(UnexpectedValueException::class);

        $reducer(Result::pure(fn () => 1), Result::success(1));
    }
}
