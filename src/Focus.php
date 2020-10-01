<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Focus
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
     * @param mixed[] $xs
     * @return mixed
     * @throws \LogicException
     */
    public function __invoke(array $xs)
    {
        if (array_key_exists($this->key, $xs)) {
            return $xs[$this->key];
        }

        throw new \LogicException;
    }
}
