<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class IsArrayTest extends TestCase
{
    public function valueProvider(): array
    {
        return [
            'null' => [null],
            'true' => [true],
            'false' => [false],
            'int' => [1],
            'float' => [1.1],
            'string' => ['value'],
            // 'array' => [['value']],
            'object' => [new class
            {
            }],
            'resource' => [tmpfile()],
        ];
    }

    public function testReturnsSuccessWhenArrayGiven(): void
    {
        $test = (new Rules\IsArray)(['value']);

        $this->assertEquals($test, Result::success(['value']));
    }

    /**
     * @dataProvider valueProvider
     */
    public function testReturnsErrorWhenOtherTypeGiven($value): void
    {
        $test = (new Rules\IsArray)($value);

        $this->expectException(InvalidDataException::class);

        $test->value();
    }
}
