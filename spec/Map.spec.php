<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Bound;
use Quanta\Validation\InvalidDataException;

describe('Map::bound()', function () {

    it('should return a bound map', function () {
        $guard1 = fn () => [];
        $guard2 = fn () => [];

        $test = Map::bound($guard1, $guard2);

        expect($test)->toEqual(new Map(true, new Bound($guard1, $guard2)));
    });

});

describe('Map::merged()', function () {

    it('should return a merged map', function () {
        $guard1 = fn () => [];
        $guard2 = fn () => [];

        $test = Map::merged($guard1, $guard2);

        expect($test)->toEqual(new Map(false, new Bound($guard1, $guard2)));
    });

});

describe('Map', function () {

    beforeEach(function () {
        $this->g = stub();
    });

    context('when the map is bound', function () {

        beforeEach(function () {
            $this->rule = new Map(true, $this->g);
        });

        describe('->__invoke()', function () {

            context('when the given array is empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)([]);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given array is not empty', function () {

                context('when the guard does not throw and InvalidDataException for any value of the given array', function () {

                    it('should return an array of the values returned by the guard', function () {
                        $this->g->with('value1')->returns('result1');
                        $this->g->with('value2')->returns('result2');

                        $test = ($this->rule)(['key1' => 'value1', 'key2' => 'value2']);

                        expect($test)->toEqual(['key1' => 'result1', 'key2' => 'result2']);
                    });

                });

                context('when the guard throws an InvalidDataException for at least one value of the given array', function () {

                    it('should throw an InvalidDataException containing all the nested errors', function () {
                        $e1 = new InvalidDataException(new Error('message1'));
                        $e2 = new InvalidDataException(new Error('message2'));

                        $this->g->with('value1')->returns([]);
                        $this->g->with('value2')->throws($e1);
                        $this->g->with('value3')->returns([]);
                        $this->g->with('value4')->returns($e2);
                        $this->g->with('value5')->returns([]);

                        $test = fn () => ($this->rule)([
                            'key1' => 'value1',
                            'key2' => 'value2',
                            'key3' => 'value3',
                            'key4' => 'value4',
                            'key5' => 'value5',
                        ]);

                        expect($test)->toThrow($e1->nest('key2'));
                    });

                });

            });

        });

    });

    context('when the map is merged', function () {

        beforeEach(function () {
            $this->rule = new Map(false, $this->g);
        });

        describe('->__invoke()', function () {

            context('when the given array is empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)([]);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given array is not empty', function () {

                context('when the guard does not throw and InvalidDataException for any value of the given array', function () {

                    it('should return an array of the values returned by the guard', function () {
                        $this->g->with('value1')->returns('result1');
                        $this->g->with('value2')->returns('result2');

                        $test = ($this->rule)(['key1' => 'value1', 'key2' => 'value2']);

                        expect($test)->toEqual(['key1' => 'result1', 'key2' => 'result2']);
                    });

                });

                context('when the guard throws an InvalidDataException for at least one value of the given array', function () {

                    it('should throw an InvalidDataException containing all the nested errors', function () {
                        $error1 = new Error('message1');
                        $error2 = new Error('message2');
                        $error3 = new Error('message3');
                        $error4 = new Error('message4');

                        $e1 = new InvalidDataException($error1, $error2);
                        $e2 = new InvalidDataException($error1, $error2);

                        $this->g->with('value1')->returns([]);
                        $this->g->with('value2')->throws($e1);
                        $this->g->with('value3')->returns([]);
                        $this->g->with('value4')->throws($e2);
                        $this->g->with('value5')->returns([]);

                        $test = fn () => ($this->rule)([
                            'key1' => 'value1',
                            'key2' => 'value2',
                            'key3' => 'value3',
                            'key4' => 'value4',
                            'key5' => 'value5',
                        ]);

                        expect($test)->toThrow(new InvalidDataException(
                            $error1->nest('key2'),
                            $error2->nest('key2'),
                            $error3->nest('key4'),
                            $error4->nest('key4'),
                        ));
                    });

                });

            });

        });

    });

});
