<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;

final class HasKey
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param mixed[] $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke(array $x): array
    {
        return key_exists($this->key, $x) ? [] : [
            new Error(sprintf('%s is required', $this->key), self::class, [
                'key' => $this->key,
            ]),
        ];
    }
}
