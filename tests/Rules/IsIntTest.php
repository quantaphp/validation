<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class IsIntTest extends TestCase
{
    public function valueProvider(): array
    {
        return [
            'null' => [null],
            'true' => [true],
            'false' => [false],
            // 'int' => [1],
            'float' => [1.1],
            'string' => ['value'],
            'array' => [['value']],
            'object' => [new class
            {
            }],
            'resource' => [tmpfile()],
        ];
    }

    public function testReturnsSuccessWhenIntGiven(): void
    {
        $test = (new Rules\IsInt)(1);

        $this->assertEquals($test, Result::success(1));
    }

    /**
     * @dataProvider valueProvider
     */
    public function testReturnsErrorWhenOtherTypeGiven($value): void
    {
        $test = (new Rules\IsInt)($value);

        $this->expectException(InvalidDataException::class);

        $test->value();
    }
}
