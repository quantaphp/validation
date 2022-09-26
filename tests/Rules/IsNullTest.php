<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class IsNullTest extends TestCase
{
    protected function valueProvider(): array
    {
        return [
            // 'null' => [null],
            'true' => [true],
            'false' => [false],
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
        $test = (new Rules\IsNull)(null);

        $this->assertEquals($test, Result::success(null));
    }

    /**
     * @dataProvider valueProvider
     */
    public function testReturnsErrorWhenOtherTypeGiven($value): void
    {
        $test = (new Rules\IsNull)($value);

        $this->expectException(InvalidDataException::class);

        $test->value();
    }
}
