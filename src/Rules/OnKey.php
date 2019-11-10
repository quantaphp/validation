<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class OnKey
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __invoke(array $data): InputInterface
    {
        return key_exists($this->key, $data)
            ? new Success($data[$this->key], $this->key)
            : new Failure(new Error(sprintf('%s is required', $this->key)));
    }
}
