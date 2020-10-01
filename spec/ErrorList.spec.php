<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\ErrorList;

describe('ErrorList', function () {

    context('when there is no error', function () {

        beforeEach(function () {
            $this->errors = new ErrorList;
        });

        describe('->errors()', function () {

            it('should return an empty array', function () {
                $test = $this->errors->errors();

                expect($test)->toEqual([]);
            });

        });

    });

    context('when there is at least one error', function () {

        beforeEach(function () {
            $this->errors = new ErrorList(
                $this->error1 = new Error('error1'),
                $this->error2 = new Error('error2'),
                $this->error3 = new Error('error3'),
            );
        });

        describe('->errors()', function () {

            context('when no key is given', function () {

                it('should return the array of errors', function () {
                    $test = $this->errors->errors();

                    expect($test[0])->toBe($this->error1);
                    expect($test[1])->toBe($this->error2);
                    expect($test[2])->toBe($this->error3);
                });

            });

            context('when at least one key is given', function () {

                it('should return the array of errors nested with the given keys', function () {
                    $test = $this->errors->errors('key1', 'key2');

                    expect($test[0])->toBe($this->error1);
                    expect($test[1])->toBe($this->error2);
                    expect($test[2])->toBe($this->error3);
                    expect($test[0]->keys())->toEqual(['key1', 'key2']);
                    expect($test[1]->keys())->toEqual(['key1', 'key2']);
                    expect($test[2]->keys())->toEqual(['key1', 'key2']);
                });

            });

        });

    });

});
