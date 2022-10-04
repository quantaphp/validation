<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Rules;
use Quanta\Validation\Types;
use Quanta\Validation\Result;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\Reducers\VariadicReducer;
use Quanta\Validation\Reducers\CombinedReducer;
use Quanta\Validation\Reducers\ReducerInterface;

final class TestClassValidationTest
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

final class TestAbstractInputValidationTest extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory;
    }
}

final class ValidationTest extends TestCase
{
    public function testFactoryReturnsAnInstanceOfValidation(): void
    {
        $this->assertInstanceOf(Validation::class, Validation::factory());
    }

    public function testFactoryReturnsAnEmptyInstance(): void
    {
        $result = Result::success(1);

        $validation = Validation::factory();

        $test = $validation($result);

        $this->assertSame($test, $result);
    }

    public function testItComposeRulesReturningSuccess(): void
    {
        $result = Result::success('result');

        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $rule1->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success(2));
        $rule2->expects($this->once())->method('__invoke')->with(2)->willReturn(Result::success(3));
        $rule3->expects($this->once())->method('__invoke')->with(3)->willReturn($result);

        $validation = Validation::factory()->rule($rule1, $rule2, $rule3);

        $test = $validation(Result::success(1));

        $this->assertSame($test, $result);
    }

    public function testItShortcutSubsequentRulesWhenARuleReturnsAnError(): void
    {
        $error = Result::error('default');

        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $rule1->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success(2));
        $rule2->expects($this->once())->method('__invoke')->with(2)->willReturn($error);
        $rule3->expects($this->never())->method('__invoke');

        $validation = Validation::factory()->rule($rule1, $rule2, $rule3);

        $test = $validation(Result::success(1));

        $this->assertSame($test, $error);
    }

    /**
     * Test for rule() function and syntax sugar.
     */

    public function validationProvider(): array
    {
        return [
            'empty' => [Validation::factory(), 0],
            'one rule' => [Validation::factory()->rule(fn () => Result::success(1)), 1],
            'many rules' => [Validation::factory()->rule(fn () => Result::success(1), fn () => Result::success(2)), 2],
        ];
    }

    public function validationAndRulesProvider(): array
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
    public function testRuleAddAWrappedRuleForNameOfAbstractInputImplementation(Validation $validation): void
    {
        $test = $validation->rule(TestAbstractInputValidationTest::class);

        $this->assertNotSame($test, $validation);
        $this->assertEquals($test, $validation->rule(new Rules\WrappedCallable([TestAbstractInputValidationTest::class, 'from'])));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRuleAddAWrappedRuleForClassName(Validation $validation): void
    {
        $test = $validation->rule(TestClassValidationTest::class);

        $this->assertNotSame($test, $validation);
        $this->assertEquals($test, $validation->rule(new Rules\WrappedClass(TestClassValidationTest::class)));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRuleThrowsForANonClassNameString(Validation $validation): void
    {
        $this->expectException(InvalidArgumentException::class);

        $validation->rule('nonclassname');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testKeyWithOneKeyAddsARequiredRule(Validation $validation): void
    {
        $test = $validation->key('key');

        $this->assertNotSame($test, $validation);
        $this->assertEquals($test, $validation->rule(new Rules\Required('key')));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testKeyWithManyKeysAddsManyIsArrayAndRequiredRule(Validation $validation): void
    {
        $test = $validation->key('key1', 'key2', 'key3');

        $this->assertNotSame($test, $validation);
        $this->assertEquals($test, $validation->rule(
            new Rules\Required('key1'),
            new Rules\IsArray,
            new Rules\Required('key2'),
            new Rules\IsArray,
            new Rules\Required('key3'),
        ));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testRequiredAddsARequiredRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->required('key');
        $test2 = $validation->required('key', ...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\Required('key')));
        $this->assertEquals($test2, $validation->rule(new Rules\Required('key')));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testOptionalAddsAnOptionalRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->optional('key');
        $test2 = $validation->optional('key', 'default');
        $test3 = $validation->optional('key', 'default', ...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertNotSame($test3, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\Optional('key', null)));
        $this->assertEquals($test2, $validation->rule(new Rules\Optional('key', 'default')));
        $this->assertEquals($test3, $validation->rule(new Rules\Optional('key', 'default')));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testNullAddsAIsNullRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->null();
        $test2 = $validation->null(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsNull));
        $this->assertEquals($test2, $validation->rule(new Rules\IsNull));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testBoolAddsAIsBoolRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->bool();
        $test2 = $validation->bool(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsBool));
        $this->assertEquals($test2, $validation->rule(new Rules\IsBool));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testIntAddsAIsIntRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->int();
        $test2 = $validation->int(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsInt));
        $this->assertEquals($test2, $validation->rule(new Rules\IsInt, ...$rules));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testFloatAddsAIsFloatRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->float();
        $test2 = $validation->float(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsFloat));
        $this->assertEquals($test2, $validation->rule(new Rules\IsFloat, ...$rules));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testStringAddsAIsStringRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->string();
        $test2 = $validation->string(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsString));
        $this->assertEquals($test2, $validation->rule(new Rules\IsString, ...$rules));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testArrayAddsAIsArrayRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->array();
        $test2 = $validation->array(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsArray));
        $this->assertEquals($test2, $validation->rule(new Rules\IsArray, ...$rules));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testNullableAddsANullableRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->nullable();
        $test2 = $validation->nullable(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\Nullable));
        $this->assertEquals($test2, $validation->rule(new Rules\Nullable, ...$rules));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testTrimmedAddsATrimmedRule(Validation $validation, array $rules): void
    {
        $test1 = $validation->trimmed();
        $test2 = $validation->trimmed(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\Trimmed));
        $this->assertEquals($test2, $validation->rule(new Rules\Trimmed, ...$rules));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testPositiveIntergerAddsAIsIntAndType(Validation $validation, array $rules): void
    {
        $test1 = $validation->positiveInteger();
        $test2 = $validation->positiveInteger(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsInt, Types\PositiveInteger::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsInt, Types\PositiveInteger::class));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testStrictlyPositiveIntergerAddsAIsIntAndType(Validation $validation, array $rules): void
    {
        $test1 = $validation->strictlyPositiveInteger();
        $test2 = $validation->strictlyPositiveInteger(...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsInt, Types\StrictlyPositiveInteger::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsInt, Types\StrictlyPositiveInteger::class));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testNonEmptyStringAddsAIsStringAndType(Validation $validation, array $rules): void
    {
        $test1 = $validation->nonEmptyString();
        $test2 = $validation->nonEmptyString(false);
        $test3 = $validation->nonEmptyString(true);
        $test4 = $validation->nonEmptyString(false, ...$rules);
        $test5 = $validation->nonEmptyString(true, ...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertNotSame($test3, $validation);
        $this->assertNotSame($test4, $validation);
        $this->assertNotSame($test5, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\NonEmptyString::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsString, Types\NonEmptyString::class));
        $this->assertEquals($test3, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\NonEmptyString::class));
        $this->assertEquals($test4, $validation->rule(new Rules\IsString, Types\NonEmptyString::class));
        $this->assertEquals($test5, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\NonEmptyString::class));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testEmailAddsAIsStringAndType(Validation $validation, array $rules): void
    {
        $test1 = $validation->nonEmptyString();
        $test2 = $validation->nonEmptyString(false);
        $test3 = $validation->nonEmptyString(true);
        $test4 = $validation->nonEmptyString(false, ...$rules);
        $test5 = $validation->nonEmptyString(true, ...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertNotSame($test3, $validation);
        $this->assertNotSame($test4, $validation);
        $this->assertNotSame($test5, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\Email::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsString, Types\Email::class));
        $this->assertEquals($test3, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\Email::class));
        $this->assertEquals($test4, $validation->rule(new Rules\IsString, Types\Email::class));
        $this->assertEquals($test5, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\Email::class));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testUrlAddsAIsStringAndType(Validation $validation, array $rules): void
    {
        $test1 = $validation->nonEmptyString();
        $test2 = $validation->nonEmptyString(false);
        $test3 = $validation->nonEmptyString(true);
        $test4 = $validation->nonEmptyString(false, ...$rules);
        $test5 = $validation->nonEmptyString(true, ...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertNotSame($test3, $validation);
        $this->assertNotSame($test4, $validation);
        $this->assertNotSame($test5, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\Url::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsString, Types\Url::class));
        $this->assertEquals($test3, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\Url::class));
        $this->assertEquals($test4, $validation->rule(new Rules\IsString, Types\Url::class));
        $this->assertEquals($test5, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\Url::class));
    }

    /**
     * @dataProvider validationAndRulesProvider
     */
    public function testIpAddressAddsAIsStringAndType(Validation $validation, array $rules): void
    {
        $test1 = $validation->nonEmptyString();
        $test2 = $validation->nonEmptyString(false);
        $test3 = $validation->nonEmptyString(true);
        $test4 = $validation->nonEmptyString(false, ...$rules);
        $test5 = $validation->nonEmptyString(true, ...$rules);

        $this->assertNotSame($test1, $validation);
        $this->assertNotSame($test2, $validation);
        $this->assertNotSame($test3, $validation);
        $this->assertNotSame($test4, $validation);
        $this->assertNotSame($test5, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\IpAddress::class));
        $this->assertEquals($test2, $validation->rule(new Rules\IsString, Types\IpAddress::class));
        $this->assertEquals($test3, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\IpAddress::class));
        $this->assertEquals($test4, $validation->rule(new Rules\IsString, Types\IpAddress::class));
        $this->assertEquals($test5, $validation->rule(new Rules\IsString, new Rules\Trimmed, Types\IpAddress::class));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testVariadicReturnsAReducerForNameOfAbstractInputImplementation(Validation $validation, int $nb): void
    {
        $test = $validation->variadic(TestAbstractInputValidationTest::class);

        $reducer = VariadicReducer::from(TestAbstractInputValidationTest::class);

        if ($nb == 0) {
            $this->assertEquals($test, $reducer);
        } else {
            $this->assertEquals($test, new CombinedReducer($validation->array(), $reducer));
        }
    }

    /**
     * @dataProvider validationProvider
     */
    public function testVariadicResturnsAReducerForValidation(Validation $validation1, int $nb): void
    {
        $validation2 = Validation::factory();

        $test = $validation1->variadic($validation2);

        $reducer = VariadicReducer::from($validation2);

        if ($nb == 0) {
            $this->assertEquals($test, $reducer);
        } else {
            $this->assertEquals($test, new CombinedReducer($validation1->array(), $reducer));
        }
    }

    /**
     * @dataProvider validationProvider
     */
    public function testVariadicResturnsAReducerForReducerInterface(Validation $validation, int $nb): void
    {
        $reducer = $this->createMock(ReducerInterface::class);

        $test = $validation->variadic($reducer);

        $reducer = VariadicReducer::from($reducer);

        if ($nb == 0) {
            $this->assertEquals($test, $reducer);
        } else {
            $this->assertEquals($test, new CombinedReducer($validation->array(), $reducer));
        }
    }

    /**
     * @dataProvider validationProvider
     */
    public function testVariadicThrowsForString(Validation $validation): void
    {
        $this->expectException(InvalidArgumentException::class);

        $test = $validation->variadic('nonclassname');
    }
}
