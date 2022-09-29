<?php

declare(strict_types=1);

namespace Quanta\Validation;

use Quanta\ValidationInterface;

final class Result
{
    const SUCCESS = 1;
    const ERROR = 2;

    /**
     * Return a successful result containing the given value.
     */
    public static function unit(mixed $value): self
    {
        return self::success($value);
    }

    /**
     * Return a result containing a pure function that can be applied.
     */
    public static function pure(callable $f): self
    {
        return self::success(new Pure($f));
    }

    /**
     * Alias of unit.
     *
     * Success sounds better for functions returning either success or error.
     *
     * Also allows to set the value as default and to set the nesting level.
     */
    public static function success(mixed $value, bool $final = false, string ...$keys): self
    {
        return new self(self::SUCCESS, $value, [], $final, ...$keys);
    }

    /**
     * Return an error result containing a single error from the given parameters.
     *
     * @param mixed[] $params
     */
    public static function error(string $label, string $default, array $params = [], string ...$keys): self
    {
        return self::errors(new Error($label, $default, $params, ...$keys));
    }

    /**
     * Return an error result containing the given errors.
     */
    public static function errors(Error $error, Error ...$errors): self
    {
        return new self(self::ERROR, null, [$error, ...$errors]);
    }

    /**
     * Turn the given function into a function with results as parameters and return value.
     *
     * (a -> b -> c -> ...) -> (Result<a> -> Rersult<b> -> Result<c> -> ...)
     *
     * @return callable(self ...$xs): self
     */
    public static function liftn(callable $f): callable
    {
        return function (self ...$xs) use ($f): self {
            return array_reduce($xs, fn ($f, $x) => self::apply($f)($x), self::pure($f));
        };
    }

    /**
     * Turn the given result containing a pure function into a function taking one result as parameter.
     *
     * Result<Pure<f>> -> (Result<a> -> Result<Pure<g>>)
     * - where f = a -> b -> c -> d -> ...
     * - where g = b -> c -> d -> ...
     *
     * @return callable(self): self
     */
    public static function apply(self $f): callable
    {
        if ($f->status == self::ERROR) {
            return function (self $x) use ($f): self {
                return match ($x->status) {
                    self::SUCCESS => $f,
                    self::ERROR => self::errors(...$f->errors, ...$x->errors),
                };
            };
        }

        if (!$f->value instanceof Pure) {
            throw new \UnexpectedValueException(
                sprintf(
                    'Apply can only be used on a Result containing an instance of %s, %s found',
                    Pure::class,
                    gettype($f->value)
                ),
            );
        }

        return function (self $x) use ($f): self {
            return match ($x->status) {
                self::SUCCESS => self::success($f->value->curry($x->value)),
                self::ERROR => $x,
            };
        };
    }

    /**
     * Turn the given rule into a composable function.
     *
     * (a -> Result<b>) -> (Result<a> -> Result<b>)
     *
     * @return callable(self): self
     */
    public static function bind(callable $f): callable
    {
        return function (self $x) use ($f): self {
            if ($x->final) {
                return $x;
            }

            if ($x->status == self::ERROR) {
                return $x;
            }

            $y = $f($x->value);

            if (!$y instanceof self) {
                throw new \UnexpectedValueException(
                    sprintf('Rule must return an instance of %s, %s returned', self::class, gettype($y))
                );
            }

            if (count($x->keys) === 0) {
                return $y;
            }

            return match ($y->status) {
                self::SUCCESS => self::success($y->value, $y->final, ...$x->keys, ...$y->keys),
                self::ERROR => self::errors(...array_map(fn ($e) => $e->nest(...$x->keys), $y->errors))
            };
        };
    }

    /**
     * Turn the given validation function into a variadic validation function taking a Result<array> as parameter.
     *
     * ((Result<Pure<f>> -> Result<a>) -> Result<Pure<f>>) -> ((Result<Pure<f>> -> Result<a[]>) -> Result<Pure<f>>)
     * - where f = a -> a -> a -> a -> ...
     *
     * @return callable(self, self): self
     */
    public static function variadic(ValidationInterface $validation): callable
    {
        return function (self $factory, self $result) use ($validation): self {
            if ($result->status == self::ERROR) {
                return $validation($factory, $result);
            }

            if (is_iterable($result->value)) {
                foreach ($result->value as $key => $value) {
                    $factory = $validation($factory, self::success($value, $result->final, ...$result->keys, ...[(string) $key]));
                }

                return $factory;
            }

            throw new \UnexpectedValueException(
                sprintf(
                    'Variadic validation can only be used with a Result containing an iterable, %s found',
                    gettype($result->value)
                )
            );
        };
    }

    /**
     * @var string[]
     */
    private array $keys;

    /**
     * @param int<1, 2> $status
     * @param Error[]   $errors
     */
    private function __construct(
        private int $status,
        private mixed $value,
        private array $errors = [],
        private bool $final = false,
        string ...$keys,
    ) {
        $this->keys = $keys;
    }

    /**
     * Return the value of a successful Result or throw an InvalidDataException when the
     * result is an error.
     */
    public function value(): mixed
    {
        if ($this->status == self::ERROR) {
            throw new InvalidDataException(...$this->errors);
        }

        if ($this->value instanceof Pure) {
            return ($this->value)();
        }

        return $this->value;
    }
}
