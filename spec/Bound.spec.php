<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Error;
use Quanta\Validation\Bound;
use Quanta\Validation\InvalidDataException;

describe('Bound', function () {

    context('when there is no guard', function () {

        beforeEach(function () {
            $this->guard = new Bound;
        });

        describe('->__invoke()', function () {

            it('should return the given value', function () {
                $test = ($this->guard)('value');

                expect($test)->toEqual('value');
            });

        });

    });

    context('when there is at least one guard', function () {

        beforeEach(function () {
            $this->guard = new Bound(
                $this->guard1 = stub(),
                $this->guard2 = stub(),
                $this->guard3 = stub(),
                $this->guard4 = stub(),
                $this->guard5 = stub(),
            );
        });

        describe('->__invoke()', function () {

            context('when no guard is throwing an InvalidDataException', function () {

                it('should reduce the guards and return the last value', function () {
                    $this->guard1->with('value1')->returns('value2');
                    $this->guard2->with('value2')->returns('value3');
                    $this->guard3->with('value3')->returns('value4');
                    $this->guard4->with('value4')->returns('value5');
                    $this->guard5->with('value5')->returns('value6');

                    $test = ($this->guard)('value1');

                    expect($test)->toEqual('value6');
                });

            });

            context('when at least one guard throws an InvalidDataException', function () {

                it('should throw the first InvalidDataException', function () {
                    $e1 = new InvalidDataException(new Error('message1'));
                    $e2 = new InvalidDataException(new Error('message2'));

                    $this->guard1->with('value1')->returns('value2');
                    $this->guard2->with('value2')->throws($e1);
                    $this->guard3->with('value3')->returns('value4');
                    $this->guard4->with('value4')->throws($e2);
                    $this->guard5->with('value5')->throws('value6');

                    $test = fn () => ($this->guard)('value1');

                    expect($test)->toThrow($e1);
                });

            });

        });

    });

});
