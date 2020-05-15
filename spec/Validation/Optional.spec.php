<?php

declare(strict_types=1);

use Quanta\Validation\Optional;

describe('Optional', function () {

    beforeEach(function () {
        $this->fallback = new Optional('value');
    });

    describe('->__invoke()', function () {

        it('should return the value', function () {
            $test = ($this->fallback)('key');

            expect($test)->toEqual('value');
        });

    });

});
