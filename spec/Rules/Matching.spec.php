<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\Rules\Matching;

describe('Matching', function () {

    beforeEach(function () {
        $this->rule = new Matching('/pattern/');
    });

    describe('->__invoke()', function () {

        context('when the given string does not match the pattern', function () {

            it('should return an error', function () {
                $test = ($this->rule)('value');

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

        context('when the given string is matching the pattern', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)('somepatterntest');

                expect($test)->toEqual([]);
            });

        });

    });

});
