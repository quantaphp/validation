<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\ErrorFormatter;
use Quanta\Validation\ErrorFormatterInterface;

describe('ErrorFormatter', function () {

    beforeEach(function () {
        $this->formatter = new ErrorFormatter;
    });

    it('should implement ErrorFormatterInterface', function () {
        expect($this->formatter)->toBeAnInstanceOf(ErrorFormatterInterface::class);
    });

    describe('->__invoke()', function () {

        context('when the error message contains %s', function () {

            beforeEach(function () {
                $this->error = Error::from('%%s message');
            });

            context('when the error has no key', function () {

                it('should return the error message', function () {
                    $test = ($this->formatter)($this->error);

                    expect($test)->toEqual('%s message');
                });
            });

            context('when the error has one key', function () {

                it('should return the error message with the key', function () {
                    $error = $this->error->nest('key');

                    $test = ($this->formatter)($error);

                    expect($test)->toEqual('key message');
                });
            });

            context('when the error has more than one key', function () {

                it('should return the error message with the key and the path', function () {
                    $error = $this->error->nest('key1', 'key2', 'key3');

                    $test = ($this->formatter)($error);

                    expect($test)->toEqual('[key1][key2] key3 message');
                });
            });
        });

        context('when the error message does not contain %s', function () {

            beforeEach(function () {
                $this->error = Error::from('message');
            });

            context('when the error has no key', function () {

                it('should return the error message', function () {
                    $test = ($this->formatter)($this->error);

                    expect($test)->toEqual('message');
                });
            });

            context('when the error has one key', function () {

                it('should return the error message with the path', function () {
                    $error = $this->error->nest('key');

                    $test = ($this->formatter)($error);

                    expect($test)->toEqual('[key] message');
                });
            });

            context('when the error has more than one key', function () {

                it('should return the error message with the path', function () {
                    $error = $this->error->nest('key1', 'key2');

                    $test = ($this->formatter)($error);

                    expect($test)->toEqual('[key1][key2] message');
                });
            });
        });
    });
});
