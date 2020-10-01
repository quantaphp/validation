<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Rule;
use Quanta\Validation\Guard;
use Quanta\Validation\Error;

describe('Rule::guard()', function () {

    beforeEach(function () {
        $this->predicate = stub();
    });

    context('when no label and params are given', function () {

        it('should return a new Rule with default label and params wrapped in a Guard', function () {
            $test = Rule::guard($this->predicate, 'message');

            expect($test)->toEqual(new Guard(new Rule($this->predicate, 'message')));
        });

    });

    context('when a label and params are given', function () {

        it('should return a new Rule with the given label and params wrapped in a Guard', function () {
            $test = Rule::guard($this->predicate, 'message', 'label', ['key' => 'value']);

            expect($test)->toEqual(new Guard(new Rule($this->predicate, 'message', 'label', ['key' => 'value'])));
        });

    });

});

describe('Rule', function () {

    beforeEach(function () {
        $this->predicate = stub();
    });

    context('when there is no label and param', function () {

        beforeEach(function () {
            $this->rule = new Rule($this->predicate, 'message');
        });

        describe('->__invoke()', function () {

            context('when the predicate is true for the given value', function () {

                it('should return an empty array', function () {
                    $this->predicate->with('value')->returns(true);

                    $test = ($this->rule)('value');

                    expect($test)->toEqual([]);
                });

            });

            context('when the predicate is false for the given value', function () {

                it('should return one error with the message', function () {
                    $this->predicate->with('value')->returns(false);

                    $test = ($this->rule)('value');

                    expect($test)->toEqual([new Error('message', '', [])]);
                });

            });

        });

    });

    context('when there is a label and params', function () {

        beforeEach(function () {
            $this->rule = new Rule($this->predicate, 'message', 'label', ['key' => 'value']);
        });

        describe('->__invoke()', function () {

            context('when the predicate is true for the given value', function () {

                it('should return an empty array', function () {
                    $this->predicate->with('value')->returns(true);

                    $test = ($this->rule)('value');

                    expect($test)->toEqual([]);
                });

            });

            context('when the predicate is false for the given value', function () {

                it('should return one error with the message, the label and the params', function () {
                    $this->predicate->with('value')->returns(false);

                    $test = ($this->rule)('value');

                    expect($test)->toEqual([new Error('message', 'label', ['key' => 'value'])]);
                });

            });

        });

    });

});
