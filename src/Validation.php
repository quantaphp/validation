<?php

declare(strict_types=1);

namespace Quanta;

use Quanta\Validation\Error;
use Quanta\Validation\HasKey;
use Quanta\Validation\Scoped;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\RuleInterface;
use Quanta\Validation\InputInterface;

use Quanta\Validation\Rules\Seq;
use Quanta\Validation\Rules\HasType;

final class Validation
{
    private string $key;

    private RuleInterface $rule;

    public static function combine(callable ...$fs): callable
    {
        $f = array_shift($fs) ?? false;

        return $f === false
            ? fn (array $xs) => new Success($xs)
            : fn (array $xs) => $f($xs)->bind(...$fs);
    }

    public static function shape(array $fs): callable
    {
        $fs = array_map([self::class, 'translate'], array_keys($fs), $fs);

        return function (array $xs) use ($fs) {
            $inputs = array_map(fn (callable $f) => $f($xs), $fs);

            $input = array_shift($inputs) ?? false;

            return $input === false ? new Success($xs) : $input->merge(...$inputs);
        };
    }

    private static function translate(string $key, $elem): callable
    {
        if (is_array($elem)) {
            return self::combine(
                new HasKey($key),
                new self($key, new HasType('array')),
                new Scoped($key),
                self::shape($elem)
            );
        }

        if ($elem instanceof RuleInterface) {
            return self::combine(
                new HasKey($key),
                new self($key, $elem),
            );
        }

        throw new \InvalidArgumentException;
    }

    public function __construct(string $key, RuleInterface $rule)
    {
        $this->key = $key;
        $this->rule = $rule;
    }

    public function __invoke(array $xs): InputInterface
    {
        $x = $xs[$this->key];

        return count($errors = ($this->rule)($x)) == 0
            ? new Success([$this->key => $x])
            : (new Failure(...$errors))->nested($this->key);
    }
}
