<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class IsBoolTest extends TestCase
{
    public function valueProvider(): array
    {
        return [
            'null' => [null],
            // 'true' => [true],
            // 'false' => [false],
            'int' => [1],
            'float' => [1.1],
            'string' => ['value'],
            'array' => [['value']],
            'object' => [new class
            {
            }],
            'resource' => [tmpfile()],
        ];
    }

    public function testReturnsSuccessWhenTrueGiven(): void
    {
        $test = (new Rules\IsBool)(true);

        $this->assertEquals($test, Result::success(true));
    }

    public function testReturnsSuccessWhenFalseGiven(): void
    {
        $test = (new Rules\IsBool)(false);

        $this->assertEquals($test, Result::success(false));
    }

    /**
     * @dataProvider valueProvider
     */
    public function testReturnsErrorWhenOtherTypeGiven($value): void
    {
        $test = (new Rules\IsBool)($value);

        $this->expectException(InvalidDataException::class);

        $test->value();
    }
}
