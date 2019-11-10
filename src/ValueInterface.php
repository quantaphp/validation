<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface ValueInterface
{
    /**
     * Return the value.
     *
     * @return mixed
     */
    public function value();
}
