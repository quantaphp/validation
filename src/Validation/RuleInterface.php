<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface RuleInterface
{
    /**
     * @param string    $name
     * @param mixed     $x
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke(string $name, $x): array;
}
