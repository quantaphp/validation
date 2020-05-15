<?php

declare(strict_types=1);

use Quanta\Validation\Error;
use Quanta\Validation\Required;
use Quanta\Validation\InvalidDataException;

describe('Required', function () {

    describe('->__invoke()', function () {

        it('should throw an InvalidDataException', function () {
            $fallback = new Required;

            $test = fn () => $fallback('key');

            expect($test)->toThrow(new InvalidDataException(
                new Error('key is required', Required::class, ['key' => 'key']),
            ));
        });

    });

});
