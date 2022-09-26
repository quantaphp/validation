<?php

declare(strict_types=1);

require_once __DIR__ . '/TestErrorFormatter.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\VariadicValidation;
use Quanta\ValidationInterface;

final class VariadicValidationTest extends TestCase
{
    public function testFromReturnsAnInstanceOfVariadicValidation(): void
    {
        $test1 = VariadicValidation::from(Validation::factory());
        $test2 = VariadicValidation::from($test1);

        $this->assertInstanceOf(VariadicValidation::class, $test1);
        $this->assertInstanceOf(VariadicValidation::class, $test2);
    }

    public function testImplementsValidationInterface(): void
    {
        $this->assertInstanceOf(ValidationInterface::class, VariadicValidation::from(Validation::factory()));
    }
}
