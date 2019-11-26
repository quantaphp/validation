<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface RuleInterface
{
    /**
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke($x): array;
}
