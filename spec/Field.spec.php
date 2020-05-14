<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Error;
use Quanta\Validation\Bound;
use Quanta\Validation\Field;

describe('Field::required()', function () {

    it('should return a required field with the given key and the bound rules', function () {
        $rule1 = fn () => [];
        $rule2 = fn () => [];

        $test = Field::required('key', $rule1, $rule2);

        expect($test)->toEqual(new Field('key', true, new Bound($rule1, $rule2)));
    });

});

describe('Field::optional()', function () {

    it('should return an optional field with the given key and the bound rules', function () {
        $rule1 = fn () => [];
        $rule2 = fn () => [];

        $test = Field::optional('key', $rule1, $rule2);

        expect($test)->toEqual(new Field('key', false, new Bound($rule1, $rule2)));
    });

});

describe('Field', function () {

    beforeEach(function () {
        $this->f = stub();
    });

    context('when the field is required', function () {

        beforeEach(function () {
            $this->rule = new Field('key', true, $this->f);
        });

        describe('->__invoke()', function () {

            context('when the given array does not have the required key', function () {

                it('should return an array containing an error', function () {
                    $test = ($this->rule)(['test' => 'value']);

                    expect($test)->toEqual([new Error(
                        sprintf(Field::ERROR, 'key'),
                        Field::class,
                        ['key' => 'key']
                    )]);
                });

            });

            context('when the given array has the required key', function () {

                context('when the rule returns an empty array for the given value', function () {

                    it('should return an empty array', function () {
                        $this->f->with('value')->returns([]);

                        $test = ($this->rule)(['key' => 'value']);

                        expect($test)->toEqual([]);
                    });

                });

                context('when the rule returns an array of errors', function () {

                    it('should return the array of errors', function () {
                        $error1 = new Error('message1');
                        $error2 = new Error('message2');

                        $this->f->with('value')->returns([$error1, $error2]);

                        $test = ($this->rule)(['key' => 'value']);

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(2);
                        expect($test[0])->toBe($error1);
                        expect($test[1])->toBe($error2);
                    });

                });

            });

        });

    });

    context('when the field is required', function () {

        beforeEach(function () {
            $this->rule = new Field('key', false, $this->f);
        });

        describe('->__invoke()', function () {

            context('when the given array does not have the required key', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)(['test' => 'value']);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given array has the required key', function () {

                context('when the rule returns an empty array for the given value', function () {

                    it('should return an empty array', function () {
                        $this->f->with('value')->returns([]);

                        $test = ($this->rule)(['key' => 'value']);

                        expect($test)->toEqual([]);
                    });

                });

                context('when the rule returns an array of errors', function () {

                    it('should return the array of errors', function () {
                        $error1 = new Error('message1');
                        $error2 = new Error('message2');

                        $this->f->with('value')->returns([$error1, $error2]);

                        $test = ($this->rule)(['key' => 'value']);

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(2);
                        expect($test[0])->toBe($error1);
                        expect($test[1])->toBe($error2);
                    });

                });

            });

        });

    });

});
