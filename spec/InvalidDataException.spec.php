<?php

declare(strict_types=1);

use Quanta\Validation\InvalidDataException;

use Quanta\Validation\Error;

describe('InvalidDataException', function () {

    context('when there is no error', function () {

        it('should throw an exception', function () {
            $test = fn () => new InvalidDataException;

            expect($test)->toThrow();
        });

    });

    context('when there is at least one error', function () {

        beforeEach(function () {
            $this->exception = new InvalidDataException(
                $this->error1 = new Error('message1'),
                $this->error2 = new Error('message2'),
            );
        });

        it('should implement Throwable', function () {
            expect($this->exception)->toBeAnInstanceOf(Throwable::class);
        });

        describe('->nest()', function () {

            it('should return the same InvalidDataException', function () {
                $test = $this->exception->nest('key');

                expect($test)->toBe($this->exception);
            });

            it('should nest the errors', function () {
                $test = $this->exception->nest('key');

                expect($test->errors())->toEqual([
                    $this->error1->nest('key'),
                    $this->error2->nest('key'),
                ]);
            });

        });

        describe('->errors()', function () {

            it('should return an array containing the errors', function () {
                $test = $this->exception->errors();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(2);
                expect($test[0])->toBe($this->error1);
                expect($test[1])->toBe($this->error2);
            });

        });

    });

});
