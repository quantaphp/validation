<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class RequiredTest extends TestCase
{
    public function testReturnsNestedSuccessWhenKeyExistsInGivenArray(): void
    {
        $test = (new Rules\Required('key'))(['key' => 'value']);

        $this->assertEquals($test, Result::success('value', false, 'key'));
    }

    public function testReturnsErrorWhenKeyDoesNotExistInGivenArray(): void
    {
        $test = (new Rules\Required('key'))([]);

        $this->expectException(InvalidDataException::class);

        $test->value();
    }
}
