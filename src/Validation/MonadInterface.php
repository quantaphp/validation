<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface MonadInterface
{
    public function bind(callable ...$fs): MonadInterface;
}
