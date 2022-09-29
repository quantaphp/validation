<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\VariadicValidation;
use Quanta\ValidationInterface;
use Quanta\Validation\Result;

final class VariadicValidationTest extends TestCase
{
    private $validation;

    protected function setUp(): void
    {
        $this->validation = $this->createMock(ValidationInterface::class);
    }

    public function testFromReturnsAnInstanceOfVariadicValidation(): void
    {
        $this->assertInstanceOf(VariadicValidation::class, VariadicValidation::from($this->validation));
    }

    public function testImplementsValidationInterface(): void
    {
        $this->assertInstanceOf(ValidationInterface::class, VariadicValidation::from($this->validation));
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
    public function testItWorksAsExpected(Result $init, array $results): void
    {
        $factories = [
            Result::pure(fn () => 1),
            Result::pure(fn () => 2),
            Result::pure(fn () => 3),
            Result::pure(fn () => 4),
        ];

        $this->validation->expects($this->exactly(3))
            ->method('__invoke')
            ->willReturnCallback(function ($factory, $result) use ($factories, $results) {
                if ($factory === $factories[0] && $result == $results[0]) return $factories[1];
                if ($factory === $factories[1] && $result == $results[1]) return $factories[2];
                if ($factory === $factories[2] && $result == $results[2]) return $factories[3];
            });

        $validation = VariadicValidation::from($this->validation);

        $test = $validation($factories[0], $init);

        $this->assertSame($test, $factories[3]);
    }

    /**
     * @dataProvider variadicProvider
     */
    public function testItComposeRules(Result $init, array $results): void
    {
        $factories = [
            Result::pure(fn () => 1),
            Result::pure(fn () => 2),
            Result::pure(fn () => 3),
            Result::pure(fn () => 4),
        ];

        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $rule1->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success(2));
        $rule2->expects($this->once())->method('__invoke')->with(2)->willReturn(Result::success(3));
        $rule3->expects($this->once())->method('__invoke')->with(3)->willReturn($init);

        $this->validation->expects($this->exactly(3))
            ->method('__invoke')
            ->willReturnCallback(function ($factory, $result) use ($factories, $results) {
                if ($factory === $factories[0] && $result == $results[0]) return $factories[1];
                if ($factory === $factories[1] && $result == $results[1]) return $factories[2];
                if ($factory === $factories[2] && $result == $results[2]) return $factories[3];
            });

        $validation = VariadicValidation::from(
            $this->validation,
            Result::bind($rule1),
            Result::bind($rule2),
            Result::bind($rule3)
        );

        $test = $validation($factories[0], Result::success(1));

        $this->assertSame($test, $factories[3]);
    }

    public function testItWorksAsExpectedForError(): void
    {
        $factory = Result::pure(fn () => 1);
        $error = Result::error('label1', 'default1');

        $expected = Result::error('label2', 'default2');

        $this->validation->expects($this->exactly(1))
            ->method('__invoke')
            ->with($factory, $error)
            ->willReturn($expected);

        $validation = VariadicValidation::from($this->validation);

        $test = $validation($factory, $error);

        $this->assertSame($test, $expected);
    }

    public function testItThrowsForSuccessContainingANonIterableValue(): void
    {
        $this->validation->expects($this->never())->method('__invoke');

        $validation = VariadicValidation::from($this->validation);

        $this->expectException(UnexpectedValueException::class);

        $validation(Result::pure(fn () => 1), Result::success(1));
    }
}
