<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Result
{
    const SUCCESS = 1;
    const ERROR = 2;

    /**
     * Return a successful result containing the given value.
     *
     * Unit sounds better for wrapping an initial value.
     */
    public static function unit(mixed $value): self
    {
        return self::success($value);
    }

    /**
     * Return a function that can be applied.
     */
    public static function pure(callable $f): self
    {
        return self::success(new Pure($f));
    }

    /**
     * Alias of unit.
     *
     * Success sounds better for functions returning either success or error.
     */
    public static function success(mixed $value): self
    {
        return new self(self::SUCCESS, $value);
    }

    /**
     * Return a successful result containing the given value and short-circuiting
     * subsequent validations.
     */
    public static function final(mixed $value): self
    {
        return new self(self::SUCCESS, $value, [], true);
    }

    /**
     * Return an error result creating a single error from the given template and variables.
     */
    public static function error(string $label, string $default, ...$params): self
    {
        return self::errors(Error::from($label, $default, ...$params));
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
     * Make use of apply so errors are merged.
     *
     * (a -> b -> c -> ...) -> (Result<a> -> Rersult<b> -> Result<c> -> ...)
     */
    public static function liftn(callable $f): callable
    {
        return function (self ...$xs) use ($f): self {
            return array_reduce($xs, fn ($f, $x) => Result::apply($f)($x), Result::unit($f));
        };
    }

    /**
     * Turn the given result containing a function into a function taking one result as parameter.
     *
     * (Result<a -> b -> c -> ...>) -> (Result<a> -> Rersult<b -> c -> ...>)
     */
    public static function apply(Result $result): callable
    {
        if ($result->status == self::SUCCESS) {
            if (!$result->value instanceof Pure) {
                throw new \LogicException(
                    sprintf('Apply can only be used on a Result containing an instance of %s', Pure::class)
                );
            }

            return function (self $x) use ($result): self {
                return match ($x->status) {
                    self::SUCCESS => self::success($result->value->curry($x->value)),
                    self::ERROR => $x,
                };
            };
        }

        return function (self $x) use ($result): self {
            return match ($x->status) {
                self::SUCCESS => $result,
                self::ERROR => self::errors(...$result->errors, ...$x->errors),
            };
        };
    }

    /**
     * Turn the given validation function into a composable validation function.
     *
     * A Result is returned when the validation
     *
     * (a -> Result<b>) -> (Result<a> -> Result<b>)
     */
    public static function bind(callable $f): callable
    {
        return function (self $result) use ($f): self {
            if ($result->final) {
                return $result;
            }

            if ($result->status == self::ERROR) {
                return $result;
            }

            $value = $f($result->value);

            if (!$value instanceof self) {
                throw new \UnexpectedValueException(
                    sprintf('Rule must return an instance of %s, %s returned', self::class, gettype($value))
                );
            }

            if ($value->status == self::ERROR) {
                return $value->nest(...$result->keys);
            }

            return $value;
        };
    }

    private array $keys;

    /**
     * @param int                                   $status
     * @param mixed                                 $value
     * @param \Quanta\Validation\Error[]   $errors
     * @param boolean                               $final
     * @param string                                ...$keys
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

    /**
     * Dont treat the result as default value anymore.
     */
    public function undefault(): self
    {
        if ($this->status == self::SUCCESS && $this->final) {
            return self::success($this->value);
        }

        return $this;
    }

    /**
     * When the result is an error, nest them within the given keys.
     */
    public function nest(string ...$keys): self
    {
        if (count($keys) == 0) {
            return $this;
        }

        if ($this->status == self::ERROR) {
            $errors = array_map(fn ($error) => $error->nest(...$keys), $this->errors);

            return self::errors(...$errors);
        }

        return new self(
            $this->status,
            $this->value,
            $this->errors,
            $this->final,
            ...$keys,
            ...$this->keys,
        );
    }
}
