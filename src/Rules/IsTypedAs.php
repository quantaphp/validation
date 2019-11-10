<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsTypedAs
{
    const MAP = [
        'bool' => [['boolean'], 'must be a boolean'],
        'boolean' => [['boolean'], 'must be a boolean'],
        'int' => [['integer'], 'must be an integer'],
        'integer' => [['integer'], 'must be an integer'],
        'float' => [['integer', 'double'], 'must be a float'],
        'double' => [['integer', 'double'], 'must be a float'],
        'number' => [['integer', 'double'], 'must be a number'],
        'string' => [['string'], 'must be a string'],
        'array' => [['array'], 'must be an array'],
        'resource' => [['resource'], 'must be an resource'],
        'null' => [['null'], 'must be null'],
    ];

    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function __invoke($value): InputInterface
    {
        $expected = strtolower($this->type);

        if (key_exists($expected, self::MAP)) {
            [$valid, $message] = self::MAP[$expected];

            return in_array(strtolower(gettype($value)), $valid)
                ? new Success($value)
                : new Failure(new Error($message));
        }

        return is_object($value) && $value instanceof $this->type
            ? new Success($value)
            : new Failure(new Error('must be an instance of %s', $this->type));
    }
}
