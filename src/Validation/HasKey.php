<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

final class HasKey
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __invoke(array $xs): InputInterface
    {
        return key_exists($this->key, $xs)
            ? new Success($xs)
            : new Failure(new Error(sprintf('%s is required', $this->key), self::class, [
                'key' => $this->key,
            ]));
    }
}
