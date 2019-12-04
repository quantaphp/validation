<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class Type
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

    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke($x): array
    {
        $expected = strtolower($this->type);

        if (key_exists($expected, self::MAP)) {
            [$valid, $message] = self::MAP[$expected];

            return in_array(strtolower(gettype($x)), $valid) ? [] : [
                new Error($message, self::class, [
                    'value' => $x,
                    'type' => $this->type,
                ]),
            ];
        }

        return is_object($x) && $x instanceof $this->type ? [] : [
            new Error(sprintf('must be an instance of %s', $this->type), self::class, [
                'value' => $x, 'type' => $this->type,
            ]),
        ];
    }
}
