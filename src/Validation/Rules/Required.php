<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Result;

final class Required
{
    public function __construct(private string $key)
    {
    }

    /**
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        $result = array_key_exists($this->key, $data)
            ? Result::success($data[$this->key])
            : Result::error(self::class, '{key} is required');

        return $result->nest($this->key);
    }
}
