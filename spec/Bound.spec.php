<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Error;
use Quanta\Validation\Bound;

describe('Bound', function () {

    context('when there is no rule', function () {

        beforeEach(function () {
            $this->rule = new Bound;
        });

        describe('->__invoke()', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)('value');

                expect($test)->toEqual([]);
            });

        });

    });

    context('when there is at least one rule', function () {

        beforeEach(function () {
            $this->rule = new Bound(
                $this->rule1 = stub(),
                $this->rule2 = stub(),
                $this->rule3 = stub(),
                $this->rule4 = stub(),
                $this->rule5 = stub(),
            );
        });

        describe('->__invoke()', function () {

            context('when there is no failing rule', function () {

                it('should return an empty array', function () {
                    $this->rule1->with('value')->returns([]);
                    $this->rule2->with('value')->returns([]);
                    $this->rule3->with('value')->returns([]);
                    $this->rule4->with('value')->returns([]);
                    $this->rule5->with('value')->returns([]);

                    $test = ($this->rule)('value');

                    expect($test)->toEqual([]);
                });

            });

            context('when there is at least one failing rule', function () {

                it('should return the errors of the first failing rule', function () {
                    $error1 = new Error('message1');
                    $error2 = new Error('message2');
                    $error3 = new Error('message3');
                    $error4 = new Error('message4');

                    $this->rule1->with('value')->returns([]);
                    $this->rule2->with('value')->returns([$error1, $error2]);
                    $this->rule3->with('value')->returns([]);
                    $this->rule4->with('value')->returns([$error3, $error4]);
                    $this->rule5->with('value')->returns([]);

                    $test = ($this->rule)('value');

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(2);
                    expect($test[0])->toBe($error1);
                    expect($test[1])->toBe($error2);
                });

            });

        });

    });

});
