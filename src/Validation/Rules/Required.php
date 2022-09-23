<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class Required
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        return array_key_exists($this->key, $data)
            ? Result::success($data[$this->key], false, $this->key)
            : Result::error(self::class, '{key} is required', [], $this->key);
    }
}
