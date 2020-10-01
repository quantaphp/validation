<?php

declare(strict_types=1);

use Quanta\Validation\Error;

describe('Error::nested()', function () {

    context('when no label and params are given', function () {

        it('should return an error with default label and params nested with the given key', function () {
            $test = Error::nested('key', 'message');

            expect($test)->toEqual((new Error('message', '', []))->nest('key'));
        });

    });

    context('when a label and params are given', function () {

        it('should return an error with the given label and params nested with the given key', function () {
            $test = Error::nested('key', 'message', 'label', ['key' => 'value']);

            expect($test)->toEqual((new Error('message', 'label', ['key' => 'value']))->nest('key'));
        });

    });

});

describe('Error', function () {

    context('when there is no label and params', function () {

        beforeEach(function () {
            $this->error = new Error('message');
        });

        describe('->nest()', function () {

            it('should return the same Error', function () {
                $test = $this->error->nest('key');

                expect($test)->toBe($this->error);
            });

        });

        describe('->keys()', function () {

            it('should return the keys', function () {
                $test = $this->error->nest()->nest('key2', 'key3')->nest('key1')->keys();

                expect($test)->toEqual(['key1', 'key2', 'key3']);
            });

        });

        describe('->message()', function () {

            it('should return the message', function () {
                $test = $this->error->message();

                expect($test)->toEqual('message');
            });

        });

        describe('->label()', function () {

            it('should return an empty string', function () {
                $test = $this->error->label();

                expect($test)->toEqual('');
            });

        });

        describe('->params()', function () {

            it('should return an empty array', function () {
                $test = $this->error->params();

                expect($test)->toEqual([]);
            });

        });

    });

    context('when there is a label and params', function () {

        beforeEach(function () {
            $this->error = new Error('message', 'label', ['key' => 'value']);
        });

        describe('->nest()', function () {

            it('should return the same error', function () {
                $test = $this->error->nest('key');

                expect($test)->toBe($this->error);
            });

        });

        describe('->keys()', function () {

            it('should return the nested keys in reverse order', function () {
                $test = $this->error->nest()->nest('key2', 'key3')->nest('key1')->keys();

                expect($test)->toEqual(['key1', 'key2', 'key3']);
            });

        });

        describe('->message()', function () {

            it('should return the message', function () {
                $test = $this->error->message();

                expect($test)->toEqual('message');
            });

        });

        describe('->label()', function () {

            it('should return the label', function () {
                $test = $this->error->label();

                expect($test)->toEqual('label');
            });

        });

        describe('->params()', function () {

            it('should return the params', function () {
                $test = $this->error->params();

                expect($test)->toEqual(['key' => 'value']);
            });

        });

    });

});
