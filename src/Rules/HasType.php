<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class HasType
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

    public function __invoke($value): array
    {
        $expected = strtolower($this->type);

        if (key_exists($expected, self::MAP)) {
            [$valid, $message] = self::MAP[$expected];

            return in_array(strtolower(gettype($value)), $valid) ? [] : [
                new Error($message, self::class, ['type' => $this->type])
            ];
        }

        return is_object($value) && $value instanceof $this->type ? [] : [
            new Error(
                sprintf('must be an instance of %s', $this->type),
                self::class,
                ['type' => $this->type],
            )
        ];
    }
}
