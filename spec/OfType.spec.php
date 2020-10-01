<?php

declare(strict_types=1);

use Quanta\Validation\Guard;
use Quanta\Validation\Error;
use Quanta\Validation\OfType;

describe('OfType::guard()', function () {

    it('should return a new OfType wrapped in a Guard', function () {
        $test = OfType::guard('string');

        expect($test)->toEqual(new Guard(new OfType('string')));
    });

});

describe('OfType', function () {

    context('when the expected type is bool', function () {

        beforeEach(function () {
            $this->rule = new OfType('bool');
        });

        context('when the given value is true', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(true);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is false', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(false);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not a boolean', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is boolean', function () {

        beforeEach(function () {
            $this->rule = new OfType('boolean');
        });

        context('when the given value is true', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(true);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is false', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(false);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not a boolean', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is int', function () {

        beforeEach(function () {
            $this->rule = new OfType('int');
        });

        context('when the given value is an integer', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not an integer', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is integer', function () {

        beforeEach(function () {
            $this->rule = new OfType('integer');
        });

        context('when the given value is an integer', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not an integer', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is float', function () {

        beforeEach(function () {
            $this->rule = new OfType('float');
        });

        context('when the given value is an integer', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is a float', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1.1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is neither an integer nor a float', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is double', function () {

        beforeEach(function () {
            $this->rule = new OfType('double');
        });

        context('when the given value is an integer', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is a float', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1.1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is neither an integer nor a float', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is number', function () {

        beforeEach(function () {
            $this->rule = new OfType('number');
        });

        context('when the given value is an integer', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is a float', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(1.1);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is neither an integer nor a float', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is string', function () {

        beforeEach(function () {
            $this->rule = new OfType('string');
        });

        context('when the given value is a string', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)('test');

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not a string', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is array', function () {

        beforeEach(function () {
            $this->rule = new OfType('array');
        });

        context('when the given value is an array', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(['test']);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not an array', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is object', function () {

        beforeEach(function () {
            $this->rule = new OfType('object');
        });

        context('when the given value is an object', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not an object', function () {

            it('should return an error', function () {
                $test = ($this->rule)(1);

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is resource', function () {

        beforeEach(function () {
            $this->rule = new OfType('resource');
        });

        context('when the given value is a resource', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(tmpfile());

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not a resource', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is null', function () {

        beforeEach(function () {
            $this->rule = new OfType('null');
        });

        context('when the given value is null', function () {

            it('should return an empty array', function () {
                $test = ($this->rule)(null);

                expect($test)->toEqual([]);
            });

        });

        context('when the given value is not null', function () {

            it('should return an error', function () {
                $test = ($this->rule)(new class {});

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

    });

    context('when the expected type is a class name', function () {

        beforeEach(function () {
            $this->rule = new OfType(OfType::class);
        });

        context('when the given value is not an object', function () {

            it('should return an error', function () {
                $test = ($this->rule)(1);

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(1);
                expect($test[0])->toBeAnInstanceOf(Error::class);
            });

        });

        context('when the given value is an object', function () {

            context('when the given object is an instance of the class', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)(new OfType('string'));

                    expect($test)->toEqual([]);
                });

            });

            context('when the given object is not an instance of the class', function () {

                it('should return an error', function () {
                    $test = ($this->rule)(new class {});

                    expect($test)->toBeAn('array');
                    expect($test)->toHaveLength(1);
                    expect($test[0])->toBeAnInstanceOf(Error::class);
                });

            });

        });

    });

});
