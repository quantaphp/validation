<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class Optional
{
    public function __construct(private string $key, private mixed $default)
    {
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        return array_key_exists($this->key, $data)
            ? Result::success($data[$this->key], false, $this->key)
            : Result::success($this->default, true, $this->key);
    }
}
