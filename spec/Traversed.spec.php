<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\stub;

use Quanta\Validation\Error;
use Quanta\Validation\Bound;
use Quanta\Validation\Traversed;

describe('Traversed::bound()', function () {

    it('should return a bound traversed rule', function () {
        $rule1 = fn () => [];
        $rule2 = fn () => [];

        $test = Traversed::bound($rule1, $rule2);

        expect($test)->toEqual(new Traversed(true, new Bound($rule1, $rule2)));
    });

});

describe('Traversed::merged()', function () {

    it('should return a merged traversed rule', function () {
        $rule1 = fn () => [];
        $rule2 = fn () => [];

        $test = Traversed::merged($rule1, $rule2);

        expect($test)->toEqual(new Traversed(false, new Bound($rule1, $rule2)));
    });

});

describe('Traversed', function () {

    beforeEach(function () {
        $this->f = stub();
    });

    context('when the traversed rule is bound', function () {

        beforeEach(function () {
            $this->rule = new Traversed(true, $this->f);
        });

        describe('->__invoke()', function () {

            context('when the given array is empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)([]);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given array is not empty', function () {

                context('when the rule returns an empty array for all values of the given array', function () {

                    it('should return an empty array', function () {
                        $this->f->with('value1')->returns([]);
                        $this->f->with('value2')->returns([]);

                        $test = ($this->rule)(['value1', 'value2']);

                        expect($test)->toEqual([]);
                    });

                });

                context('when the rule returns an array of errors for at least one value of the given array', function () {

                    it('should return the first array of errors and nest them within the associated key', function () {
                        $error1 = (new Error('message1'))->nest('key');
                        $error2 = (new Error('message2'))->nest('key');
                        $error3 = (new Error('message3'))->nest('key');
                        $error4 = (new Error('message4'))->nest('key');

                        $this->f->with('value1')->returns([]);
                        $this->f->with('value2')->returns([$error1, $error2]);
                        $this->f->with('value3')->returns([]);
                        $this->f->with('value4')->returns([$error3, $error4]);
                        $this->f->with('value5')->returns([]);

                        $test = ($this->rule)([
                            'key1' => 'value1',
                            'key2' => 'value2',
                            'key3' => 'value3',
                            'key4' => 'value4',
                            'key5' => 'value5',
                        ]);

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(2);
                        expect($test[0])->toBe($error1);
                        expect($test[1])->toBe($error2);
                        expect($test[0]->keys())->toEqual(['key2', 'key']);
                        expect($test[1]->keys())->toEqual(['key2', 'key']);
                    });

                });

            });

        });

    });

    context('when the traversed rule is bound', function () {

        beforeEach(function () {
            $this->rule = new Traversed(false, $this->f);
        });

        describe('->__invoke()', function () {

            context('when the given array is empty', function () {

                it('should return an empty array', function () {
                    $test = ($this->rule)([]);

                    expect($test)->toEqual([]);
                });

            });

            context('when the given array is not empty', function () {

                context('when the rule returns an empty array for all values of the given array', function () {

                    it('should return an empty array', function () {
                        $this->f->with('value1')->returns([]);
                        $this->f->with('value2')->returns([]);

                        $test = ($this->rule)(['value1', 'value2']);

                        expect($test)->toEqual([]);
                    });

                });

                context('when the rule returns an array of errors for at least one value of the given array', function () {

                    it('should return an array of all errors and nest them within the associated key', function () {
                        $error1 = (new Error('message1'))->nest('key');
                        $error2 = (new Error('message2'))->nest('key');
                        $error3 = (new Error('message3'))->nest('key');
                        $error4 = (new Error('message4'))->nest('key');

                        $this->f->with('value1')->returns([]);
                        $this->f->with('value2')->returns([$error1, $error2]);
                        $this->f->with('value3')->returns([]);
                        $this->f->with('value4')->returns([$error3, $error4]);
                        $this->f->with('value5')->returns([]);

                        $test = ($this->rule)([
                            'key1' => 'value1',
                            'key2' => 'value2',
                            'key3' => 'value3',
                            'key4' => 'value4',
                            'key5' => 'value5',
                        ]);

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(4);
                        expect($test[0])->toBe($error1);
                        expect($test[1])->toBe($error2);
                        expect($test[2])->toBe($error3);
                        expect($test[3])->toBe($error4);
                        expect($test[0]->keys())->toEqual(['key2', 'key']);
                        expect($test[1]->keys())->toEqual(['key2', 'key']);
                        expect($test[2]->keys())->toEqual(['key4', 'key']);
                        expect($test[3]->keys())->toEqual(['key4', 'key']);
                    });

                });

            });

        });

    });

});
