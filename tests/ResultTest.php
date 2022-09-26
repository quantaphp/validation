<?php

declare(strict_types=1);

require_once __DIR__ . '/TestErrorFormatter.php';

use PHPUnit\Framework\TestCase;

use Quanta\Validation;
use Quanta\Validation\Pure;
use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

final class ResultTest extends TestCase
{
    public function testUnitAliasesSuccess(): void
    {
        $this->assertEquals(Result::unit(1), Result::success(1));
    }

    public function testPureAliasesSuccessWithWrappedFunction(): void
    {
        $this->assertEquals(Result::pure($f = fn () => 1), Result::success(new Pure($f)));
    }

    public function testErrorAliasesErrors(): void
    {
        $this->assertEquals(
            Result::error('label', 'default', ['p1', 'p2', 'p3'], 'key1', 'key2', 'key3'),
            Result::errors(new Error('label', 'default', ['p1', 'p2', 'p3'], 'key1', 'key2', 'key3'))
        );
    }

    public function testErrorHasDefaultParamsAndKeys(): void
    {
        $this->assertEquals(Result::error('label', 'default'), Result::errors(new Error('label', 'default')));
    }

    public function testSuccessfulResultReturnsValue(): void
    {
        $result1 = Result::success('value');
        $result2 = Result::success('value', true);
        $result3 = Result::success('value', false, 'key1', 'key2', 'key3');
        $result4 = Result::success('value', true, 'key1', 'key2', 'key3');

        $test1 = $result1->value();
        $test2 = $result2->value();
        $test3 = $result3->value();
        $test4 = $result4->value();

        $this->assertEquals($test1, 'value');
        $this->assertEquals($test2, 'value');
        $this->assertEquals($test3, 'value');
        $this->assertEquals($test4, 'value');
    }

    public function testErrorResultThrowsInvalidDataException(): void
    {
        $error1 = new Error('label1', 'default1', ['p11', 'p12', 'p13'], 'key11', 'key12', 'key13');
        $error2 = new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'key21', 'key22', 'key23');
        $error3 = new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'key31', 'key32', 'key33');

        $result = Result::errors($error1, $error2, $error3);

        try {
            $result->value();
        } catch (InvalidDataException $e) {
            [$test1, $test2, $test3] = $e->messages(new TestErrorFormatter);

            $this->assertEquals($test1, 'label1:default1:p11:p12:p13:key11:key12:key13');
            $this->assertEquals($test2, 'label2:default2:p21:p22:p23:key21:key22:key23');
            $this->assertEquals($test3, 'label3:default3:p31:p32:p33:key31:key32:key33');
        }
    }

    public function testLiftnWorksAsExpected(): void
    {
        $f = Result::liftn(fn (int ...$xs) => implode(':', $xs));

        $test1 = $f(Result::success(1), Result::success(2), Result::success(3))->value();

        $test2 = $f(
            Result::error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'),
            Result::success(2),
            Result::errors(
                new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
                new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33')
            )
        );

        $this->assertEquals($test1, '1:2:3');

        $this->assertEquals($test2, Result::errors(
            new Error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'),
            new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33')
        ));
    }

    public function testApplyWorksAsExceptedForInstanceOfPure(): void
    {
        $f = Result::apply(Result::pure(fn (int ...$xs) => implode(':', $xs)));

        $test1 = $f(Result::success(1));
        $test1 = Result::apply($test1)(Result::success(2));
        $test1 = Result::apply($test1)(Result::success(3));
        $test1 = $test1->value();

        $test2 = $f(Result::success(1));
        $test2 = Result::apply($test2)(Result::error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'));
        $test2 = Result::apply($test2)(Result::success(3));
        $test2 = Result::apply($test2)(Result::errors(
            new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33')
        ));

        $this->assertEquals($test1, '1:2:3');

        $this->assertEquals($test2, Result::errors(
            new Error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'),
            new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33')
        ));
    }

    public function testApplyWorksAsExceptedForError(): void
    {
        $f = Result::apply(Result::error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'));

        $test1 = $f(Result::success(1));
        $test1 = Result::apply($test1)(Result::success(2));
        $test1 = Result::apply($test1)(Result::success(3));

        $test2 = $f(Result::success(1));
        $test2 = Result::apply($test2)(Result::error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'));
        $test2 = Result::apply($test2)(Result::success(3));
        $test2 = Result::apply($test2)(Result::errors(
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33'),
            new Error('label4', 'default4', ['p41', 'p42', 'p43'], 'e41', 'e42', 'e43')
        ));

        $this->assertEquals($test1, Result::error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'));

        $this->assertEquals($test2, Result::errors(
            new Error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'),
            new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33'),
            new Error('label4', 'default4', ['p41', 'p42', 'p43'], 'e41', 'e42', 'e43')
        ));
    }

    public function testApplyThrowsForSuccessNotContainingAnInstanceOfPure(): void
    {
        $this->expectException(UnexpectedValueException::class);

        Result::apply(Result::success(1));
    }

    public function testBindWorksAsExpectedForFunctionReturningSuccess(): void
    {
        $f = Result::bind(fn (int $value) => Result::success($value * 2));

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));

        $this->assertEquals($test1, Result::success(2));
        $this->assertEquals($test2, Result::success(2, false, 'a1', 'a2', 'a3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));
    }

    public function testBindWorksAsExpectedForFunctionReturningNestedSuccess(): void
    {
        $f = Result::bind(fn (int $value) => Result::success($value * 2, false, 'b1', 'b2', 'b3'));

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));

        $this->assertEquals($test1, Result::success(2, false, 'b1', 'b2', 'b3'));
        $this->assertEquals($test2, Result::success(2, false, 'a1', 'a2', 'a3', 'b1', 'b2', 'b3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));
    }

    public function testBindWorksAsExpectedForFunctionReturningDefaultSuccess(): void
    {
        $f = Result::bind(fn (int $value) => Result::success($value * 2, true));

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));

        $this->assertEquals($test1, Result::success(2, true));
        $this->assertEquals($test2, Result::success(2, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));
    }

    public function testBindWorksAsExpectedForFunctionReturningNestedDefaultSuccess(): void
    {
        $f = Result::bind(fn (int $value) => Result::success($value * 2, true, 'b1', 'b2', 'b3'));

        $test1 = $f(Result::success(1));
        $test2 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test3 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));

        $this->assertEquals($test1, Result::success(2, true, 'b1', 'b2', 'b3'));
        $this->assertEquals($test2, Result::success(2, true, 'a1', 'a2', 'a3', 'b1', 'b2', 'b3'));
        $this->assertEquals($test3, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));
    }

    public function testBindWorksAsExpectedForFunctionReturningError(): void
    {
        $f = Result::bind(fn (int $value) => Result::error('label', 'default', ['value' => $value], 'c1', 'c2', 'c3'));

        $test1 = $f(Result::success(1));
        $test3 = $f(Result::success(1, false, 'a1', 'a2', 'a3'));
        $test2 = $f(Result::success(1, true));
        $test4 = $f(Result::success(1, true, 'a1', 'a2', 'a3'));
        $test5 = $f(Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));

        $this->assertEquals($test1, Result::error('label', 'default', ['value' => 1], 'c1', 'c2', 'c3'));
        $this->assertEquals($test3, Result::error('label', 'default', ['value' => 1], 'a1', 'a2', 'a3', 'c1', 'c2', 'c3'));
        $this->assertEquals($test2, Result::success(1, true));
        $this->assertEquals($test4, Result::success(1, true, 'a1', 'a2', 'a3'));
        $this->assertEquals($test5, Result::error('label', 'default', ['p1', 'p2', 'p3'], 'e1', 'e2', 'e3'));
    }

    public function testBindThrowsForCallablesNotReturningAResult(): void
    {
        $f = Result::bind(fn (int $value) => $value * 2);

        $this->expectException(UnexpectedValueException::class);

        $f(Result::success(1));
    }

    public function testVariadicWorksAsExpected(): void
    {
        $rule = fn (int $value) => $value > 0
            ? Result::success($value * 2)
            : Result::error('label', 'default', ['value' => $value], 'key1', 'key2', 'key3');

        $f = Result::variadic(Validation::factory()->rule($rule));

        $factory = Result::pure(fn (int ...$xs) => implode(':', $xs));

        $test1 = $f($factory, Result::success([1, 2, 3]))->value();
        $test2 = $f($factory, Result::success($this->iterator([1, 2, 3])))->value();
        $test3 = $f($factory, Result::success($this->iteratoragg([1, 2, 3])))->value();
        $test4 = $f($factory, Result::success([1, -2, 3, -4, 5, -6]));
        $test5 = $f($factory, Result::success($this->iterator([1, -2, 3, -4, 5, -6])));
        $test6 = $f($factory, Result::success($this->iteratoragg([1, -2, 3, -4, 5, -6])));
        $test7 = $f($factory, Result::errors(
            new Error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'),
            new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33')
        ));

        $this->assertEquals($test1, '2:4:6');
        $this->assertEquals($test2, '2:4:6');
        $this->assertEquals($test3, '2:4:6');

        $this->assertEquals($test4, Result::errors(
            new Error('label', 'default', ['value' => -2], '1', 'key1', 'key2', 'key3'),
            new Error('label', 'default', ['value' => -4], '3', 'key1', 'key2', 'key3'),
            new Error('label', 'default', ['value' => -6], '5', 'key1', 'key2', 'key3')
        ));

        $this->assertEquals($test5, Result::errors(
            new Error('label', 'default', ['value' => -2], '1', 'key1', 'key2', 'key3'),
            new Error('label', 'default', ['value' => -4], '3', 'key1', 'key2', 'key3'),
            new Error('label', 'default', ['value' => -6], '5', 'key1', 'key2', 'key3')
        ));

        $this->assertEquals($test6, Result::errors(
            new Error('label', 'default', ['value' => -2], '1', 'key1', 'key2', 'key3'),
            new Error('label', 'default', ['value' => -4], '3', 'key1', 'key2', 'key3'),
            new Error('label', 'default', ['value' => -6], '5', 'key1', 'key2', 'key3')
        ));

        $this->assertEquals($test7, Result::errors(
            new Error('label1', 'default1', ['p11', 'p12', 'p13'], 'e11', 'e12', 'e13'),
            new Error('label2', 'default2', ['p21', 'p22', 'p23'], 'e21', 'e22', 'e23'),
            new Error('label3', 'default3', ['p31', 'p32', 'p33'], 'e31', 'e32', 'e33')
        ));
    }

    public function testVariadicThrowsWhenGivingTheResultingFunctionASuccessfulResultContainingANonIterableValue(): void
    {
        $f = Result::variadic(Validation::factory());

        $factory = Result::pure(fn (int ...$xs) => implode(':', $xs));

        $this->expectException(UnexpectedValueException::class);

        $f($factory, Result::success(1));
    }

    private function iterator(array $data): Iterator
    {
        return new ArrayIterator($data);
    }

    private function iteratorAgg(array $data): IteratorAggregate
    {
        return new class($data) implements IteratorAggregate
        {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function getIterator(): Traversable
            {
                return new ArrayIterator($this->data);
            }
        };
    }
}
