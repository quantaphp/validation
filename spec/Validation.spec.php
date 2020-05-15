<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

describe('Validation', function () {

    beforeEach(function () {
        $this->factory = stub();
    });

    context('when there is no guard', function () {

        beforeEach(function () {
            $this->validation = new Validation($this->factory);
        });

        describe('->__invoke()', function () {

            it('should return the value returned by the factory with no argument', function () {
                $this->factory->returns('validated');

                $test = ($this->validation)('value');

                expect($test)->toEqual('validated');
            });

        });

    });

    context('when there is at least one guard', function () {

        beforeEach(function () {
            $this->validation = new Validation($this->factory,
                $this->guard1 = stub(),
                $this->guard2 = stub(),
                $this->guard3 = stub(),
                $this->guard4 = stub(),
                $this->guard5 = stub(),
            );
        });

        describe('->__invoke()', function () {

            context('when no guard is throwing an InvalidDataException', function () {

                it('should return the value returned by the factory with the values returned by the guards as arguments', function () {
                    $this->factory
                        ->with('value1', 'value2', 'value3', 'value4', 'value5')
                        ->returns('validated');

                    $this->guard1->with('value')->returns('value1');
                    $this->guard2->with('value')->returns('value2');
                    $this->guard3->with('value')->returns('value3');
                    $this->guard4->with('value')->returns('value4');
                    $this->guard5->with('value')->returns('value5');

                    $test = ($this->validation)('value');

                    expect($test)->toEqual('validated');
                });

            });

            context('when at least one guard throws an InvalidDataException', function () {

                it('should throw an InvalidDataException containing all the errors', function () {
                    $error1 = new Error('message1');
                    $error2 = new Error('message2');
                    $error3 = new Error('message3');
                    $error4 = new Error('message4');

                    $e1 = new InvalidDataException($error1, $error2);
                    $e2 = new InvalidDataException($error1, $error2);

                    $this->guard1->with('value')->returns('value1');
                    $this->guard2->with('value')->throws($e1);
                    $this->guard3->with('value')->returns('value3');
                    $this->guard4->with('value')->throws($e2);
                    $this->guard5->with('value')->returns('value5');

                    $test = fn () => ($this->validation)('value');

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
