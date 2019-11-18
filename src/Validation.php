<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\RuleInterface;
use Quanta\Validation\InputInterface;

final class Validation implements ValidationInterface
{
    private string $key;

    private RuleInterface $rule;

    public function __construct(string $key, RuleInterface $rule)
    {
        $this->key = $key;
        $this->rule = $rule;
    }

    public function __invoke(string $name, array $xs): InputInterface
    {
        if (key_exists($this->key, $xs)) {
            $x = $xs[$this->key];

            return count($errors = ($this->rule)($name, $x)) > 0
                ? new Success($name . '[' . $this->key . ']', [$this->key => $x])
                : new Failure(...$errors);
        }

        throw new \LogicException(sprintf('%s required', $this->key));
    }
}
