<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\InputInterface;

final class Slice
{
    public function __invoke(array $data, string $key): InputInterface
    {
        return (new OnKey($key))($data);
    }
}
