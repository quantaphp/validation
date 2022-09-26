<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Rules;
use Quanta\Validation\Result;

final class OptionalTest extends TestCase
{
    public function testReturnsNestedSuccessWhenKeyExistsInGivenArray(): void
    {
        $test = (new Rules\Optional('key', 'default'))(['key' => 'value']);

        $this->assertEquals($test, Result::success('value', false, 'key'));
    }

    public function testReturnsNestedDefaultSuccessWhenKeyDoesNotExistInGivenArray(): void
    {
        $test = (new Rules\Optional('key', 'default'))([]);

        $this->assertEquals($test, Result::success('default', true, 'key'));
    }
}
