<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Result;

final class CombinedValidation implements ValidationInterface
{
    private ValidationInterface $validation;

    private $head;

    private array $tail;

    public function __construct(ValidationInterface $validation, callable $head, callable ...$tail)
    {
        $this->validation = $validation;
        $this->head = $head;
        $this->tail = $tail;
    }

    public function __invoke(Result $factory, $data): Result
    {
        $init = ($this->head)($data);

        $reducer = fn ($result, $rule) => $rule($result);

        $result = array_reduce($this->tail, $reducer, $init)->undefault();

        return $this->bound($factory)($result);
    }

    private function bound(Result $factory): callable
    {
        return Result::bind(fn ($value) => ($this->validation)($factory, $value));
    }

    public function variadic(): VariadicValidation
    {
        return new VariadicValidation($this);
    }
}
