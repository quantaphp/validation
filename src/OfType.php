<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class OfType
{
    /**
     * @var array[]
     */
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
        'object' => [['object'], 'must be an object'],
        'resource' => [['resource'], 'must be an resource'],
        'null' => [['null'], 'must be null'],
    ];

    /**
     * @var string
     */
    private string $type;

    /**
     * @param string $type
     * @return \Quanta\Validation\Guard<mixed>
     */
    public static function guard(string $type): Guard
    {
        return new Guard(new self($type));
    }

    /**
     * @param string $type
     */
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
                new Error($message, self::class, ['value' => $x, 'type' => $this->type]),
            ];
        }

        return is_object($x) && $x instanceof $this->type ? [] : [
            new Error(
                sprintf('must be an instance of %s', $this->type),
                self::class,
                ['value' => $x, 'type' => $this->type],
            ),
        ];
    }
}
