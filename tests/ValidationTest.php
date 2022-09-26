<?php

declare(strict_types=1);

require_once __DIR__ . '/TestErrorFormatter.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\VariadicValidation;
use Quanta\ValidationInterface;
use Quanta\Validation\Rules;
use Quanta\Validation\Types;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class TestClass
{
    public function __construct($value)
    {
        $this->value = $value;
    }
}

final class ValidationTest extends TestCase
{
    public function testImplementsValidationInterface(): void
    {
        $this->assertInstanceOf(ValidationInterface::class, Validation::factory());
    }

    public function testFactoryReturnsAnInstanceOfValidation(): void
    {
        $this->assertInstanceOf(Validation::class, Validation::factory());
    }

    public function testFactoryReturnsAnEmptyInstance(): void
    {
        $x = new class
        {
        };

        $factory = Result::pure(fn (object $test) => $this->assertSame($test, $x));

        $validation = Validation::factory();

        $validation($factory, Result::success($x))->value();
    }

    /**
     * Test for invalid rules.
     */

    protected function nonRuleProvider(): array
    {
        return [
            'null' => [null],
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

    /**
     * @dataProvider nonRuleProvider
     */
    public function testRuleThrowsWhenANonCallableIsGiven($value): void
    {
        $this->expectException(InvalidArgumentException::class);

        Validation::factory()->rule($value);
    }

    /**
     * Test for composition of rules.
     */

    protected function inputProvider(): array
    {
        $rules = [
            new Rules\Required('key1'),
            new Rules\IsArray,
            new Rules\Required('key2'),
            new Rules\IsArray,
            new Rules\Required('key3'),
        ];

        $x = new class
        {
        };

        return [
            [$rules, [], false, null],
            [$rules, ['key1' => 1], false, null],
            [$rules, ['key1' => []], false, null],
            [$rules, ['key1' => ['key2' => 1]], false, null],
            [$rules, ['key1' => ['key2' => []]], false, null],
            [$rules, ['key1' => ['key2' => ['key3' => $x]]], true, $x],
        ];
    }

    /**
     * @dataProvider inputProvider
     */
    public function testItComposeRules(array $rules, array $input, bool $success, ?object $x): void
    {
        $validation = Validation::factory()->rule(...$rules);

        $factory = Result::pure(fn (object $test) => $this->assertSame($test, $x));

        $test = $validation($factory, Result::success($input));

        if (!$success) {
            $this->expectException(InvalidDataException::class);
        }

        $test->value();
    }

    /**
     * Test for variadic().
     */

    protected function variadicProvider(): array
    {
        $rules1 = [
            fn () => Result::success('rules11'),
            fn () => Result::success('rules12'),
            fn () => Result::success('rules13'),
        ];

        $rules2 = [
            fn () => Result::success('rules21'),
            fn () => Result::success('rules22'),
            fn () => Result::success('rules23'),
        ];

        return [
            [[], []],
            [$rules1, []],
            [[], $rules2],
            [$rules1, $rules2],
        ];
    }

    /**
     * @dataProvider variadicProvider
     */
    public function testVariadicReturnsAVariadicValidationWithThisRulesAndTheGivenValidation(array $rules1, array $rules2): void
    {
        $validation = Validation::factory()->rule(...$rules1);

        $test = Validation::factory()->rule(...$rules2)->variadic($validation);

        $this->assertEquals($test, VariadicValidation::from(
            $validation,
            ...array_map([Result::class, 'bind'], [...$rules2, new Rules\IsArray]),
        ));
    }

    /**
     * Test for rule() function and syntax sugar.
     */

    protected function validationProvider(): array
    {
        $rules = [
            fn () => Result::success(1),
            fn () => Result::success(2),
            fn () => Result::success(3),
        ];

        return [
            'empty' => [Validation::factory(), $rules],
            'one rule' => [Validation::factory()->rule(fn () => Result::success(1)), $rules],
            'many rules' => [Validation::factory()->rule(fn () => Result::success(1), fn () => Result::success(2)), $rules],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRuleReturnsTheSameInstanceWhenNoRuleIsGiven(Validation $validation): void
    {
        $this->assertSame($validation, $validation->rule());
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRuleReturnsANewInstanceWhenRulesAreGiven(Validation $validation): void
    {
        $this->assertNotSame($validation, $validation->rule(fn () => Result::success(1)));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRuleAddAWrappedRuleWhenAClassNameIsGiven(Validation $validation): void
    {
        $test = $validation->rule(TestClass::class);

        $this->assertEquals($test, $validation->rule(new Rules\Wrapped(fn ($x) => new TestClass($x))));
    }
    /**
     * @dataProvider validationProvider
     */
    public function testKeyWithOneKeyAddsARequiredRule(Validation $validation): void
    {
        $test = $validation->key('key');

        $this->assertEquals($test, $validation->rule(new Rules\Required('key')));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testKeyWithManyKeysAddsManyIsArrayAndRequiredRule(Validation $validation): void
    {
        $test = $validation->key('key1', 'key2', 'key3');

        $this->assertEquals($test, $validation->rule(
            new Rules\Required('key1'),
            new Rules\IsArray,
            new Rules\Required('key2'),
            new Rules\IsArray,
            new Rules\Required('key3'),
        ));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRequiredAddsARequiredRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->required('key');
        $test2 = $validation->required('key', ...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\Required('key')));
        $this->assertEquals($test2, $validation->rule(new Rules\Required('key')));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testOptionalAddsAnOptionalRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->optional('key');
        $test2 = $validation->optional('key', 'default');
        $test3 = $validation->optional('key', 'default', ...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\Optional('key', null)));
        $this->assertEquals($test2, $validation->rule(new Rules\Optional('key', 'default')));
        $this->assertEquals($test3, $validation->rule(new Rules\Optional('key', 'default')));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testNullAddsAIsNullRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->null();
        $test2 = $validation->null(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsNull));
        $this->assertEquals($test2, $validation->rule(new Rules\IsNull));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testBoolAddsAIsBoolRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->bool();
        $test2 = $validation->bool(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsBool));
        $this->assertEquals($test2, $validation->rule(new Rules\IsBool));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testIntAddsAIsIntRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->int();
        $test2 = $validation->int(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsInt));
        $this->assertEquals($test2, $validation->rule(new Rules\IsInt, ...$rules));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testFloatAddsAIsFloatRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->float();
        $test2 = $validation->float(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsFloat));
        $this->assertEquals($test2, $validation->rule(new Rules\IsFloat, ...$rules));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testStringAddsAIsStringRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->string();
        $test2 = $validation->string(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsString));
        $this->assertEquals($test2, $validation->rule(new Rules\IsString, ...$rules));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testArrayAddsAIsArrayRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->array();
        $test2 = $validation->array(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsArray));
        $this->assertEquals($test2, $validation->rule(new Rules\IsArray, ...$rules));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testNullableAddsANullableRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->nullable();
        $test2 = $validation->nullable(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\Nullable));
        $this->assertEquals($test2, $validation->rule(new Rules\Nullable, ...$rules));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testPositiveIntergerAddsAIsIntAndWrappedRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->positiveInteger();
        $test2 = $validation->positiveInteger(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsInt, Types\PositiveInteger::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsInt, Types\PositiveInteger::class));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testStrictlyPositiveIntergerAddsAIsIntAndWrappedRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->strictlyPositiveInteger();
        $test2 = $validation->strictlyPositiveInteger(...$rules);

        $this->assertEquals($test1, $validation->rule(new Rules\IsInt, Types\StrictlyPositiveInteger::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsInt, Types\StrictlyPositiveInteger::class));
    }
}
