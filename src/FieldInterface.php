<?php

declare(strict_types=1);

namespace Quanta;

interface FieldInterface extends InputInterface
{
    /**
     * Return the wrapped callable.
     *
     * @return callable
     */
    public function f(): callable;
}
