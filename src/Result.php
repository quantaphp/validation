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
        return new self(self::SUCCESS, $value);
    }

    /**
     * Alias of unit.
     *
     * Success sounds better for functions returning either success or error.
     */
    public static function success(mixed $value): self
    {
        return self::unit($value);
    }

    /**
     * Return a successful result containing the given value and short-circuiting
     * subsequent validations.
     */
    public static function final(mixed $value): self
    {
        return new self(self::SUCCESS, $value, [], true, false);
    }

    /**
     * Return an error result creating a single error from the given template and variables.
     */
    public static function error(string $template, mixed ...$xs): self
    {
        return self::errors(Error::from($template, ...$xs));
    }

    /**
     * Return an error result containing the given errors.
     */
    public static function errors(Error $error, Error ...$errors): self
    {
        return new self(self::ERROR, null, [$error, ...$errors]);
    }

    /**
     * Return a successful result containing a callable that must be executed when unwrapping value.
     *
     * This allows to not get a callable when unwrapping the result of apply.
     */
    private static function callable(callable $f): self
    {
        return new self(self::SUCCESS, $f, [], false, true);
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
            $f = $result->value;

            if (!is_callable($f)) {
                throw new \LogicException('Apply can only be used on a Result containing a callable');
            }

            return function (self $x) use ($f): self {
                return match ($x->status) {
                    self::SUCCESS => self::callable(fn (...$xs) => $f($x->value, ...$xs)),
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

            try {
                $value = $f($result->value);
            } catch (InvalidDataException $e) {
                return self::errors(...$e->errors);
            }

            if (!$value instanceof self) {
                return self::success($value);
            }

            return $value;
        };
    }

    /**
     * @param 1|2                           $status
     * @param mixed                         $value
     * @param \Quanta\Validation\Error[]    $errors
     * @param boolean                       $final
     * @param boolean                       $callable
     */
    private function __construct(
        private int $status,
        private mixed $value,
        private array $errors = [],
        private bool $final = false,
        private bool $callable = false,
    ) {
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

        if ($this->callable) {
            // should never happen
            if (!is_callable($this->value)) {
                throw new \Exception;
            }

            return ($this->value)();
        }

        return $this->value;
    }

    /**
     * When the result is an error, nest them within the given keys.
     */
    public function nest(string $key, string ...$keys): self
    {
        if ($this->status == self::ERROR) {
            $errors = array_map(fn ($e) => $e->nest($key, ...$keys), $this->errors);

            return self::errors(...$errors);
        }

        return $this;
    }
}
