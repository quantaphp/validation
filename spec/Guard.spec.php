<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\InvalidDataException;

describe('Guard', function () {

    context('when there is no rule', function () {

        beforeEach(function () {
            $this->guard = new Guard;
        });

        describe('->__invoke()', function () {

            it('should return the given value', function () {
                $test = ($this->guard)('value');

                expect($test)->toEqual('value');
            });

        });

    });

    context('when there is at least one rule', function () {

        beforeEach(function () {
            $this->rule1 = stub();
            $this->rule2 = stub();
            $this->rule3 = stub();
            $this->rule4 = stub();
            $this->rule5 = stub();

            $this->guard = new Guard(
                $this->rule1,
                $this->rule2,
                $this->rule3,
                $this->rule4,
                $this->rule5,
            );
        });

        describe('->__invoke()', function () {

            context('when all the rules return an empty array', function () {

                it('should return the given value', function () {
                    $this->rule1->with('value')->returns([]);
                    $this->rule2->with('value')->returns([]);
                    $this->rule3->with('value')->returns([]);
                    $this->rule4->with('value')->returns([]);
                    $this->rule5->with('value')->returns([]);

                    $test = ($this->guard)('value');

                    expect($test)->toEqual('value');
                });

            });

            context('when at least one rule returns a non empty array of errors', function () {

                it('should throw an InvalidDataException containing all the errors', function () {
                    $error1 = new Error('message1');
                    $error2 = new Error('message2');
                    $error3 = new Error('message3');
                    $error4 = new Error('message4');

                    $this->rule1->with('value')->returns([]);
                    $this->rule2->with('value')->returns([$error1, $error2]);
                    $this->rule3->with('value')->returns([]);
                    $this->rule4->with('value')->returns([$error3, $error4]);
                    $this->rule5->with('value')->returns([]);

                    $test = fn () => ($this->guard)('value');

                    expect($test)->toThrow(new InvalidDataException(
                        $error1,
                        $error2,
                        $error3,
                        $error4,
                    ));
                });

            });

        });

    });

});
