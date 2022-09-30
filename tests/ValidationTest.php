<?php

declare(strict_types=1);

require_once __DIR__ . '/TestCallable.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\VariadicValidation;
use Quanta\ValidationInterface;
use Quanta\Validation\Rules;
use Quanta\Validation\Types;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;

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
        $factory = $this->createMock(TestCallable::class);

        $factory->expects($this->once())->method('__invoke')->with(1)->willReturn('result');

        $validation = Validation::factory();

        $test = $validation(Result::pure($factory), Result::success(1))->value();

        $this->assertEquals($test, 'result');
    }

    public function testItComposeRulesReturningSuccess(): void
    {
        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $factory = $this->createMock(TestCallable::class);

        $rule1->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success(2));
        $rule2->expects($this->once())->method('__invoke')->with(2)->willReturn(Result::success(3));
        $rule3->expects($this->once())->method('__invoke')->with(3)->willReturn(Result::success(4));

        $factory->expects($this->once())->method('__invoke')->with(4)->willReturn('result');

        $validation = Validation::factory()->rule($rule1, $rule2, $rule3);

        $test = $validation(Result::pure($factory), Result::success(1))->value();

        $this->assertEquals($test, 'result');
    }

    public function testItShortcutValidationWhenARuleReturnsAnError(): void
    {
        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $factory = $this->createMock(TestCallable::class);

        $error = Error::from('default');

        $rule1->expects($this->once())->method('__invoke')->with(1)->willReturn(Result::success(2));
        $rule2->expects($this->once())->method('__invoke')->with(2)->willReturn(Result::errors($error));
        $rule3->expects($this->never())->method('__invoke');

        $factory->expects($this->never())->method('__invoke');

        $validation = Validation::factory()->rule($rule1, $rule2, $rule3);

        $test = $validation(Result::pure($factory), Result::success(1));

        $this->assertEquals($test, Result::errors($error));
    }

    public function testItAccumulatesSuccessAsFactoryParameters(): void
    {
        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $factory = $this->createMock(TestCallable::class);

        $rule1->expects($this->exactly(3))->method('__invoke')->willReturnMap([
            [11, Result::success(12)],
            [21, Result::success(22)],
            [31, Result::success(32)],
        ]);

        $rule2->expects($this->exactly(3))->method('__invoke')->willReturnMap([
            [12, Result::success(13)],
            [22, Result::success(23)],
            [32, Result::success(33)],
        ]);

        $rule3->expects($this->exactly(3))->method('__invoke')->willReturnMap([
            [13, Result::success(14)],
            [23, Result::success(24)],
            [33, Result::success(34)],
        ]);

        $factory->expects($this->once())->method('__invoke')->with(14, 24, 34)->willReturn('result');

        $validation = Validation::factory()->rule($rule1, $rule2, $rule3);

        $pure = Result::pure($factory);

        $pure = $validation($pure, Result::success(11));
        $pure = $validation($pure, Result::success(21));
        $pure = $validation($pure, Result::success(31));

        $test = $pure->value();

        $this->assertEquals($test, 'result');
    }

    public function testItAccumulatesErrors(): void
    {
        $error1 = Error::from('default1');
        $error2 = Error::from('default2');
        $error3 = Error::from('default3');

        $rule1 = $this->createMock(TestCallable::class);
        $rule2 = $this->createMock(TestCallable::class);
        $rule3 = $this->createMock(TestCallable::class);

        $factory = $this->createMock(TestCallable::class);

        $rule1->expects($this->exactly(3))->method('__invoke')->willReturnMap([
            [11, Result::errors($error1)],
            [21, Result::success(22)],
            [31, Result::success(32)],
        ]);

        $rule2->expects($this->exactly(2))->method('__invoke')->willReturnMap([
            [22, Result::errors($error2)],
            [32, Result::success(33)],
        ]);

        $rule3->expects($this->exactly(1))->method('__invoke')->willReturnMap([
            [33, Result::errors($error3)],
        ]);

        $factory->expects($this->never())->method('__invoke');

        $validation = Validation::factory()->rule($rule1, $rule2, $rule3);

        $pure = Result::pure($factory);

        $pure = $validation($pure, Result::success(11));
        $pure = $validation($pure, Result::success(21));
        $pure = $validation($pure, Result::success(31));

        $test = $pure;

        $this->assertEquals($test, Result::errors($error1, $error2, $error3));
    }

    public function testItThrowsForResultNotContainingAnInstanceOfPure(): void
    {
        $factory = Result::unit(fn () => 1);

        $validation = Validation::factory();

        $this->expectException(UnexpectedValueException::class);

        $validation($factory, Result::success(1));
    }

    /**
     * Test for variadic().
     */

    public function variadicProvider(): array
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

    public function validationProvider(): array
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
        $this->assertEquals($test, $validation->rule(new Rules\Wrapped([TestAbstractInputValidationTest::class, 'from'])));
    }

    /**
     * @dataProvider validationProvider
     */
    public function testRuleAddAWrappedRuleForClassName(Validation $validation): void
    {
        $test1 = $validation->rule(TestClassValidationTest::class);

        $this->assertNotSame($test1, $validation);
        $this->assertEquals($test1, $validation->rule(new Rules\Wrapped(fn ($x) => new TestClassValidationTest($x))));

        $value = null;

        $test2 = $test1(Result::pure(function ($x) use (&$value) {
            $value = $x->value;
            return $x;
        }), Result::success(1))->value();

        $this->assertEquals($test2, new TestClassValidationTest($value));
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
     * @dataProvider validationProvider
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
}
