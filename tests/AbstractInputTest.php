<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\InvalidDataException;

final class TestInput extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('test1')->string(),
            $v->key('test2')->variadic($v->int())
        );
    }

    public function __construct(string $x, int ...$xs)
    {
        $this->x = $x;
        $this->xs = $xs;
    }

    public function value(): string
    {
        return implode(':', [$this->x, ...$this->xs]);
    }
}

final class AbstractInputTest extends TestCase
{
    public function testFromReturnsInstanceForValidData(): void
    {
        $test = TestInput::from(['test1' => 'test', 'test2' => [1, 2, 3, 4, 5]]);

        $this->assertInstanceOf(TestInput::class, $test);
        $this->assertEquals($test->value(), 'test:1:2:3:4:5');
    }

    public function testFromThrowsInvalidDataExceptionForInvalidData(): void
    {
        $this->expectException(InvalidDataException::class);

        TestInput::from([]);
    }
}
