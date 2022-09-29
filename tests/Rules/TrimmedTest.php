<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;

final class TrimmedTest extends TestCase
{
    public function testReturnsSuccessWithTrimmedString(): void
    {
        $test = (new Rules\Trimmed)('  value  ');

        $this->assertEquals($test, Result::success('value'));
    }
}
