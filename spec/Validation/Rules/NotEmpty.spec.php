<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\Rules\NotEmpty;

describe('NotEmpty', function () {

    beforeEach(function () {
        $this->rule = new NotEmpty;
    });

    describe('->__invoke()', function () {

        context('when the given value is a string', function () {

            context('when the given string is empty', function () {

                it('should return an error', function () {
                    $test = ($this->rule)('');

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given string contains only whitespaces', function () {

                it('should return an error', function () {
                    $test = ($this->rule)('   ');

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given string is not empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)('value');

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is an array', function () {

            context('when the given array is empty', function () {

                it('should return an error', function () {
                    $test = ($this->rule)([]);

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given array is not empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)(['value']);

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is a countable object', function () {

            context('when the given countable object is empty', function () {

                it('should return an error', function () {
                    $test = ($this->rule)(new ArrayIterator([]));

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

            context('when the given countable object is not empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)(new ArrayIterator(['value']));

                    expect($test)->toEqual([]);
                });

            });

        });

        context('when the given value is neither a string, an array nor a countable object', function () {

            it('should throw an InvalidArgumentException', function () {
                $test = fn () => ($this->rule)(new class {});

                expect($test)->toThrow(new InvalidArgumentException);
            });

        });

    });

});
