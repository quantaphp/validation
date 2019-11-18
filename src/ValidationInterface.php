<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\InputInterface;

interface ValidationInterface
{
    public function __invoke(string $name, array $xs): InputInterface;
}
