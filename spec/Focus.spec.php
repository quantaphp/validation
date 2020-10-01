<?php

declare(strict_types=1);

use Quanta\Validation\Focus;

describe('Focus', function () {

    beforeEach(function () {
        $this->guard = new Focus('key');
    });

    describe('->__invoke()', function () {

        context('when the given array has the key', function () {

            it('should return the value associated with the key', function () {
                $test = ($this->guard)(['key' => 'value']);

                expect($test)->toEqual('value');
            });

        });

        context('when the given array does not have the key', function () {

            it('should throw a LogicException', function () {
                $test = fn () => ($this->guard)(['other' => 'value']);

                expect($test)->toThrow(new LogicException);
            });

        });

    });

});
