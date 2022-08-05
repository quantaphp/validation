<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\Result;
use Quanta\Validation\InvalidDataException;

describe('Result::unit()', function () {

    it('should return a successful Result with the given value', function () {
        $test = Result::unit('test');

        expect($test->value())->toEqual('test');
    });
});

describe('Result::success()', function () {

    it('should return a successful Result with the given value', function () {
        $test = Result::success('test');

        expect($test->value())->toEqual('test');
    });
});

describe('Result::final()', function () {

    it('should return a successful Result with the given value and short-circuiting subsequent validations', function () {
        $test = Result::final('test');

        $f = Result::bind(fn () => Result::success('other'));

        expect($f($test)->value())->toEqual('test');
    });
});

describe('Result::error()', function () {

    context('when no extra parameters are given', function () {

        it('should return an error Result containing one error proxying the given parameters', function () {
            $test = Result::error('message');

            $errors = [];

            try {
                $test->value();
            } catch (InvalidDataException $e) {
                $errors = $e->errors;
            }

            expect($test)->toBeAnInstanceOf(Result::class);
            expect($errors)->toBeAn('array');
            expect($errors)->toHaveLength(1);
            expect($errors[0])->toEqual(Error::from('message'));
        });
    });

    context('when extra parameters are given', function () {

        it('should return an error Result containing one error proxying the given parameters', function () {
            $test = Result::error('message %s - %s message', 'param1', 'param2');

            $errors = [];

            try {
                $test->value();
            } catch (InvalidDataException $e) {
                $errors = $e->errors;
            }

            expect($test)->toBeAnInstanceOf(Result::class);
            expect($errors)->toBeAn('array');
            expect($errors)->toHaveLength(1);
            expect($errors[0])->toEqual(Error::from('message %s - %s message', 'param1', 'param2'));
        });
    });
});

describe('Result::errors()', function () {

    it('should return an error Result containing the given errors', function () {
        $error1 = Error::from('message1');
        $error2 = Error::from('message2');
        $error3 = Error::from('message3');

        $test = Result::errors($error1, $error2, $error3);

        $errors = [];

        try {
            $test->value();
        } catch (InvalidDataException $e) {
            $errors = $e->errors;
        }

        expect($test)->toBeAnInstanceOf(Result::class);
        expect($errors)->toBeAn('array');
        expect($errors)->toHaveLength(3);
        expect($errors[0])->toBe($error1);
        expect($errors[1])->toBe($error2);
        expect($errors[2])->toBe($error3);
    });
});

describe('Result::liftn()', function () {

    it('should return the given callable but taking Result as parameters and returnig a Result', function () {
        $test = Result::liftn(fn (int $a, int $b, int $c) => $a + $b + $c);

        $error1 = Error::from('error1');
        $error2 = Error::from('error2');
        $error3 = Error::from('error3');

        $result1 = $test(Result::success(1), Result::success(2), Result::success(3));
        $result2 = $test(Result::success(1), Result::errors($error1), Result::errors($error2, $error3));

        expect($result1)->toBeAnInstanceOf(Result::class);
        expect($result1->value())->toEqual(6);

        $errors = [];

        try {
            $result2->value();
        } catch (InvalidDataException $e) {
            $errors = $e->errors;
        }

        expect($result2)->toBeAnInstanceOf(Result::class);
        expect($errors)->toBeAn('array');
        expect($errors)->toHaveLength(3);
        expect($errors[0])->toBe($error1);
        expect($errors[1])->toBe($error2);
        expect($errors[2])->toBe($error3);
    });
});

describe('Result::apply()', function () {

    context('when the given Result is a successful Result', function () {

        context('when the given Result does not contain a callable', function () {

            it('should throw a LogicException', function () {
                $test = fn () => Result::apply(Result::success(1));

                expect($test)->toThrow(new LogicException);
            });
        });

        context('when the given Result contains a callable', function () {

            it('should return a callable taking a Result as parameter and returning a Result containing the curryed callable', function () {
                $f = fn (int $a, int $b, int $c) => $a + $b + $c;

                $error1 = Error::from('error1');
                $error2 = Error::from('error2');
                $error3 = Error::from('error3');

                $result1 = Result::unit($f);
                $result1 = Result::apply($result1)(Result::success(1));
                $result1 = Result::apply($result1)(Result::success(2));
                $result1 = Result::apply($result1)(Result::success(3));

                expect($result1)->toBeAnInstanceOf(Result::class);
                expect($result1->value())->toEqual(6);

                $result2 = Result::success($f);
                $result2 = Result::apply($result2)(Result::success(1));
                $result2 = Result::apply($result2)(Result::errors($error1));
                $result2 = Result::apply($result2)(Result::errors($error2, $error3));

                $errors = [];

                try {
                    $result2->value();
                } catch (InvalidDataException $e) {
                    $errors = $e->errors;
                }

                expect($result2)->toBeAnInstanceOf(Result::class);
                expect($errors)->toBeAn('array');
                expect($errors)->toHaveLength(3);
                expect($errors[0])->toBe($error1);
                expect($errors[1])->toBe($error2);
                expect($errors[2])->toBe($error3);
            });
        });
    });

    context('when the given Result is an error Result', function () {

        it('should return a callable taking a Result as parameter and returning an error Result accumulating errors', function () {
            $error1 = Error::from('error1');
            $error2 = Error::from('error2');
            $error3 = Error::from('error3');
            $error4 = Error::from('error4');

            $result = Result::errors($error1);
            $result = Result::apply($result)(Result::success(1));
            $result = Result::apply($result)(Result::errors($error2));
            $result = Result::apply($result)(Result::errors($error3, $error4));

            $errors = [];

            try {
                $result->value();
            } catch (InvalidDataException $e) {
                $errors = $e->errors;
            }

            expect($result)->toBeAnInstanceOf(Result::class);
            expect($errors)->toBeAn('array');
            expect($errors)->toHaveLength(4);
            expect($errors[0])->toBe($error1);
            expect($errors[1])->toBe($error2);
            expect($errors[2])->toBe($error3);
            expect($errors[3])->toBe($error4);
        });
    });
});

describe('Result::bind()', function () {

    context('when the given callable returns a successful result', function () {

        it('should return the given callable but taking a Result as parameter and returning a successful Result', function () {
            $error1 = Error::from('error1');
            $error2 = Error::from('error2');

            $test = Result::bind(fn (int $i) => Result::success($i + 1));

            $result1 = $test(Result::success(1));
            $result2 = $test(Result::errors($error1, $error2));

            expect($result1)->toBeAnInstanceOf(Result::class);
            expect($result1->value())->toEqual(2);

            $errors = [];

            try {
                $result2->value();
            } catch (InvalidDataException $e) {
                $errors = $e->errors;
            }

            expect($result2)->toBeAnInstanceOf(Result::class);
            expect($errors)->toBeAn('array');
            expect($errors)->toHaveLength(2);
            expect($errors[0])->toBe($error1);
            expect($errors[1])->toBe($error2);
        });
    });

    context('when the given callable returns an error result', function () {

        it('should return the given callable but taking a Result as parameter and returning an error Result', function () {
            $error1 = Error::from('error1');
            $error2 = Error::from('error2');
            $error3 = Error::from('error3');
            $error4 = Error::from('error4');

            $test = Result::bind(fn () => Result::errors($error1, $error2));

            $result1 = $test(Result::success(1));
            $result2 = $test(Result::errors($error3, $error4));

            $errors1 = [];

            try {
                $result1->value();
            } catch (InvalidDataException $e) {
                $errors1 = $e->errors;
            }

            expect($result1)->toBeAnInstanceOf(Result::class);
            expect($errors1)->toBeAn('array');
            expect($errors1)->toHaveLength(2);
            expect($errors1[0])->toBe($error1);
            expect($errors1[1])->toBe($error2);

            $errors2 = [];

            try {
                $result2->value();
            } catch (InvalidDataException $e) {
                $errors2 = $e->errors;
            }

            expect($result2)->toBeAnInstanceOf(Result::class);
            expect($errors2)->toBeAn('array');
            expect($errors2)->toHaveLength(2);
            expect($errors2[0])->toBe($error3);
            expect($errors2[1])->toBe($error4);
        });
    });

    context('when the given callable returns any value', function () {

        it('should return the given callable but taking a Result as parameter and returning a successful Result', function () {
            $error1 = Error::from('error1');
            $error2 = Error::from('error2');

            $test = Result::bind(fn (int $i) => $i + 1);

            $result1 = $test(Result::success(1));
            $result2 = $test(Result::errors($error1, $error2));

            expect($result1)->toBeAnInstanceOf(Result::class);
            expect($result1->value())->toEqual(2);

            $errors = [];

            try {
                $result2->value();
            } catch (InvalidDataException $e) {
                $errors = $e->errors;
            }

            expect($result2)->toBeAnInstanceOf(Result::class);
            expect($errors)->toBeAn('array');
            expect($errors)->toHaveLength(2);
            expect($errors[0])->toBe($error1);
            expect($errors[1])->toBe($error2);
        });
    });

    context('when the given callable throws an InvalidDataException', function () {

        it('should return the given callable but taking a Result as parameter and returning an error Result', function () {
            $error1 = Error::from('error1');
            $error2 = Error::from('error2');
            $error3 = Error::from('error3');
            $error4 = Error::from('error4');

            $test = Result::bind(fn () => throw new InvalidDataException($error1, $error2));

            $result1 = $test(Result::success(1));
            $result2 = $test(Result::errors($error3, $error4));

            $errors1 = [];

            try {
                $result1->value();
            } catch (InvalidDataException $e) {
                $errors1 = $e->errors;
            }

            expect($result1)->toBeAnInstanceOf(Result::class);
            expect($errors1)->toBeAn('array');
            expect($errors1)->toHaveLength(2);
            expect($errors1[0])->toBe($error1);
            expect($errors1[1])->toBe($error2);

            $errors2 = [];

            try {
                $result2->value();
            } catch (InvalidDataException $e) {
                $errors2 = $e->errors;
            }

            expect($result2)->toBeAnInstanceOf(Result::class);
            expect($errors2)->toBeAn('array');
            expect($errors2)->toHaveLength(2);
            expect($errors2[0])->toBe($error3);
            expect($errors2[1])->toBe($error4);
        });
    });
});

describe('Result', function () {

    describe('->value()', function () {

        context('when the Result is successful', function () {

            context('when the Result is not a callable produced with apply', function () {

                it('should return the value', function () {
                    $result = Result::success('test');

                    $test = $result->value();

                    expect($test)->toEqual('test');
                });
            });

            context('when the Result is a callable produced with apply', function () {

                it('should return the value returned by the callable', function () {
                    $result = Result::unit(fn (int $i) => $i + 1);
                    $result = Result::apply($result)(Result::unit(1));

                    $test = $result->value();

                    expect($test)->toEqual(2);
                });
            });
        });

        context('when the Result is an error', function () {

            it('should throw an InvalidDataException with the errors', function () {
                $error1 = Error::from('error1');
                $error2 = Error::from('error2');
                $error3 = Error::from('error3');

                $result = Result::errors($error1, $error2, $error3);

                $errors = [];

                try {
                    $result->value();
                } catch (InvalidDataException $e) {
                    $errors = $e->errors;
                }

                expect($errors)->toBeAn('array');
                expect($errors)->toHaveLength(3);
                expect($errors[0])->toBe($error1);
                expect($errors[1])->toBe($error2);
                expect($errors[2])->toBe($error3);
            });
        });
    });

    describe('->nest()', function () {

        context('when the Result is successful', function () {

            it('should return the Result', function () {
                $result = Result::success(1);

                $test = $result->nest('key');

                expect($test)->toBe($result);
            });
        });

        context('when the Result is an error', function () {

            it('should return a new error Result with errors nested with the given keys', function () {
                $error1 = Error::from('error1');
                $error2 = Error::from('error2');
                $error3 = Error::from('error3');

                $result = Result::errors($error1, $error2, $error3);

                $test = $result->nest('key1', 'key2', 'key3');

                $errors = [];

                try {
                    $test->value();
                } catch (InvalidDataException $e) {
                    $errors = $e->errors;
                }

                expect($test)->toBeAnInstanceOf(Result::class);
                expect($test)->not->toBe($result);
                expect($errors)->toBeAn('array');
                expect($errors)->toHaveLength(3);
                expect($errors[0])->toBeAnInstanceOf(Error::class);
                expect($errors[0])->not->toBe($error1);
                expect($errors[0]->keys)->toEqual(['key1', 'key2', 'key3']);
                expect($errors[1])->toBeAnInstanceOf(Error::class);
                expect($errors[1])->not->toBe($error2);
                expect($errors[1]->keys)->toEqual(['key1', 'key2', 'key3']);
                expect($errors[2])->toBeAnInstanceOf(Error::class);
                expect($errors[2])->not->toBe($error3);
                expect($errors[2]->keys)->toEqual(['key1', 'key2', 'key3']);
            });
        });
    });
});
