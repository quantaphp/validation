<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;

final class NullableTest extends TestCase
{
    public function valueProvider(): array
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

    public function testReturnsDefaultSuccessWhenNullGiven(): void
    {
        $test = (new Rules\Nullable)(null);

        $this->assertEquals($test, Result::success(null, true));
    }

    /**
     * @dataProvider valueProvider
     */
    public function testReturnsSuccessWhenNonNullValueGiven($value): void
    {
        $test = (new Rules\Nullable)($value);

        $this->assertEquals($test, Result::success($value));
    }
}
