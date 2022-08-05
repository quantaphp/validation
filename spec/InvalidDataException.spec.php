<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Validation\Error;
use Quanta\Validation\ErrorFormatter;
use Quanta\Validation\ErrorFormatterInterface;
use Quanta\Validation\InvalidDataException;

describe('InvalidDataException::error()', function () {

    context('when no extra parameters are given', function () {

        it('should return an InvalidDataException containing one error proxying the given parameters', function () {
            $test = InvalidDataException::error('message');

            expect($test)->toBeAnInstanceOf(InvalidDataException::class);
            expect($test->errors)->toBeAn('array');
            expect($test->errors)->toHaveLength(1);
            expect($test->errors[0])->toEqual(Error::from('message'));
        });
    });

    context('when extra parameters are given', function () {

        it('should return an InvalidDataException containing one error proxying the given parameters', function () {
            $test = InvalidDataException::error('message %s - %s message', 'param1', 'param2');

            expect($test)->toBeAnInstanceOf(InvalidDataException::class);
            expect($test->errors)->toBeAn('array');
            expect($test->errors)->toHaveLength(1);
            expect($test->errors[0])->toEqual(Error::from('message %s - %s message', 'param1', 'param2'));
        });
    });
});

describe('InvalidDataException', function () {

    beforeEach(function () {
        $this->exception = new InvalidDataException(
            $this->error1 = Error::from('message1'),
            $this->error2 = Error::from('message2')->nest('key1'),
            $this->error3 = Error::from('message3')->nest('key3'),
        );
    });

    it('should implement Throwable', function () {
        expect($this->exception)->toBeAnInstanceOf(Throwable::class);
    });

    describe('->errors', function () {

        it('should be public', function () {
            expect($this->exception->errors)->toBeAn('array');
            expect($this->exception->errors)->toHaveLength(3);
            expect($this->exception->errors[0])->toBe($this->error1);
            expect($this->exception->errors[1])->toBe($this->error2);
            expect($this->exception->errors[2])->toBe($this->error3);
        });

        it('should be readonly', function () {
            $test = fn () => $this->exception->errors = [];

            expect($test)->toThrow();
        });
    });

    describe('->messages()', function () {

        context('when no formatter is given', function () {

            it('should use the default formatter', function () {
                $formatter = new ErrorFormatter;

                $test = $this->exception->messages();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test[0])->toEqual($formatter($this->error1));
                expect($test[1])->toEqual($formatter($this->error2));
                expect($test[2])->toEqual($formatter($this->error3));
            });
        });

        context('when a formatter is given', function () {

            it('should use it to format errors as strings', function () {
                $formatter = mock(ErrorFormatterInterface::class);

                $formatter->__invoke->with($this->error1)->returns('error1');
                $formatter->__invoke->with($this->error2)->returns('error2');
                $formatter->__invoke->with($this->error3)->returns('error3');

                $test = $this->exception->messages($formatter->get());

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test[0])->toEqual('error1');
                expect($test[1])->toEqual('error2');
                expect($test[2])->toEqual('error3');
            });
        });
    });
});
