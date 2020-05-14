<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\Rules\GreaterThanEqual;

describe('GreaterThanEqual', function () {

    beforeEach(function () {
        $this->rule = new GreaterThanEqual(1);
    });

    describe('->__invoke()', function () {

        context('when the given value is an integer', function () {

            context('when the given integer is lower than the threshold', function () {

                it('should return an error',function () {
                    $test = ($this->rule)(0);

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given integer is equal to the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(1);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given integer is greater than the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(2);

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is a float', function () {

            context('when the given float is lower than the threshold', function () {

                it('should return an error',function () {
                    $test = ($this->rule)(0.1);

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given float is equal to the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(1.0);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given float is greater than the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(2.1);

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is a string', function () {

            context('when the given string length is lower than the threshold', function () {

                it('should return an error',function () {
                    $test = ($this->rule)('');

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given string length is equal to the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)('a');

                    expect($test)->toEqual([]);
                });

            });

            context('when the given string length is greater than the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)('ab');

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is an array', function () {

            context('when the given array size is lower than the threshold', function () {

                it('should return an error',function () {
                    $test = ($this->rule)([]);

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given array size is equal to the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(['a']);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given array size is greater than the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(['a', 'b']);

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is a countable object', function () {

            context('when the given countable object size is lower than the threshold', function () {

                it('should return an error',function () {
                    $test = ($this->rule)(new ArrayIterator([]));

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given countable object size is equal to the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(new ArrayIterator(['a']));

                    expect($test)->toEqual([]);
                });

            });

            context('when the given countable object size is greater than the threshold', function () {

                it('should return an empty array',function () {
                    $test = ($this->rule)(new ArrayIterator(['a', 'b']));

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is neither an integer, a float, a string an array nor a countable object', function () {

            it('should throw an InvalidArgumentException', function () {
                $test = fn () => ($this->rule)(new class {});

                expect($test)->toThrow(new InvalidArgumentException);
            });

        });

    });

});
