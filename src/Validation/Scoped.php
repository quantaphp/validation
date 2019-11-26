<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

final class Scoped
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __invoke(array $xs): InputInterface
    {
        $x = $xs[$this->key];

        if (is_array($x)) {
            return new Success($x, $this->key);
        }

        throw new \LogicException(sprintf('%s must be an array', $this->key));
    }
}
