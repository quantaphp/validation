<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Error;
use Quanta\Validation\Bound;
use Quanta\Validation\Focus;
use Quanta\Validation\Field;
use Quanta\Validation\Required;
use Quanta\Validation\Optional;
use Quanta\Validation\InvalidDataException;

describe('Field::required()', function () {

    it('should return a field with the given key, a required fallback and the bound guards', function () {
        $guard1 = fn () => [];
        $guard2 = fn () => [];

        $test = Field::required('key', $guard1, $guard2);

        expect($test)->toEqual(new Field('key', new Required, new Bound($guard1, $guard2)));
    });

});

describe('Field::optional()', function () {

    it('should return a field with the given key, an optional fallback with the given default value and the bound guards', function () {
        $guard1 = fn () => [];
        $guard2 = fn () => [];

        $test = Field::optional('key', 'default', $guard1, $guard2);

        expect($test)->toEqual(new Field('key', new Optional('default'), new Bound($guard1, $guard2)));
    });

});

describe('Field', function () {

    beforeEach(function () {
        $this->fallback = stub();
        $this->g = stub();
        $this->guard = new Field('key', $this->fallback, $this->g);
    });

    describe('->focus()', function () {

        it('should return the field bound with a focus guard using the same key', function () {
            $test = $this->guard->focus();

            expect($test)->toEqual(new Bound($this->guard, new Focus('key')));
        });

    });

    describe('->__invoke()', function () {

        context('when the given array does not have the key', function () {

            it('should nest and return the value produced by the fallback for the key', function () {
                $this->fallback->with('key')->returns('fallback');

                $test = ($this->guard)(['test' => 'value']);

                expect($test)->toEqual(['key' => 'fallback']);
            });

        });

        context('when the given array has the key', function () {

            context('when the guard does not throw an InvalidDataException', function () {

                it('should nest and return the value returned by the guard', function () {
                    $this->g->with('value1')->returns('value2');

                    $test = ($this->guard)(['key' => 'value1']);

                    expect($test)->toEqual(['key' => 'value2']);
                });

            });

            context('when the guard throws an InvalidDataException', function () {

                it('should nest and throw the InvalidDataException', function () {
                    $e = new InvalidDataException(new Error('message'));

                    $this->g->with('value')->throws($e);

                    $test = fn () => ($this->guard)(['key' => 'value']);

                    expect($test)->toThrow($e->nest('key'));
                });

            });

        });

    });

});
