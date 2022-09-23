<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Quanta\Validation\Error;

final class ErrorTest extends TestCase
{
    public function testReturnsLabel(): void
    {
        $error = new Error('label', 'default');

        $test = $error->label();

        $this->assertEquals($test, 'label');
    }

    public function testReturnsDefault(): void
    {
        $error = new Error('label', 'default');

        $test = $error->default();

        $this->assertEquals($test, 'default');
    }

    public function testReturnsParamsWithEmptyAsDefault(): void
    {
        $params = ['a' => 1, 'b' => 2, 'c' => 3];

        $error1 = new Error('label', 'default');
        $error2 = new Error('label', 'default', $params);

        $test1 = $error1->params();
        $test2 = $error2->params();

        $this->assertEquals($test1, []);
        $this->assertEquals($test2, $params);
    }

    public function testReturnsKeysWithEmptyAsDefault(): void
    {
        $keys = ['a', 'b', 'c'];

        $error1 = new Error('label', 'default');
        $error2 = new Error('label', 'default', [], ...$keys);

        $test1 = $error1->keys();
        $test2 = $error2->keys();

        $this->assertEquals($test1, []);
        $this->assertEquals($test2, $keys);
    }
}
