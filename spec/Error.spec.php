<?php

declare(strict_types=1);

use Quanta\Validation\Error;

describe('Error::from()', function () {

    context('when no extra parameters are given', function () {

        it('should return an error with the given template', function () {
            $test = Error::from('message');

            expect($test)->toBeAnInstanceOf(Error::class);
            expect($test->keys)->toEqual([]);
            expect($test->message)->toEqual('message');
        });
    });

    context('when extra parameters are given', function () {

        it('should return an error with the given template formatted with the extra parameters', function () {
            $test = Error::from('message %s - %s message', 'param1', 'param2');

            expect($test)->toBeAnInstanceOf(Error::class);
            expect($test->keys)->toEqual([]);
            expect($test->message)->toEqual('message param1 - param2 message');
        });
    });
});

describe('Error', function () {

    beforeEach(function () {
        $this->error = Error::from('message');
    });

    describe('->nest()', function () {

        it('should return a new error with the given key', function () {
            $test = $this->error->nest('key3')->nest('key1', 'key2');

            expect($test)->not->toBe($this->error);
            expect($test->keys)->toEqual(['key1', 'key2', 'key3']);
        });
    });

    describe('->keys', function () {

        it('should be public', function () {
            $test = $this->error->nest()->nest('key2', 'key3')->nest('key1');

            expect($test->keys)->toEqual(['key1', 'key2', 'key3']);
        });

        it('should be readonly', function () {
            $test = fn () => $this->error->keys = [];

            expect($test)->toThrow();
        });
    });

    describe('->message', function () {

        it('should be public', function () {
            expect($this->error->message)->toEqual('message');
        });

        it('should be readonly', function () {
            $test = fn () => $this->error->message = '';

            expect($test)->toThrow();
        });
    });
});
