<?php declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Traversed
{
    /**
     * @var boolean
     */
    private bool $bound;

    /**
     * @var callable(T): \Quanta\Validation\Error[]
     */
    private $rule;

    /**
     * @param callable(T): \Quanta\Validation\Error[] ...$rules
     * @return \Quanta\Validation\Traversed<T>
     */
    public static function bound(callable ...$rules): self
    {
        return new self(true, new Bound(...$rules));
    }

    /**
     * @param callable(T): \Quanta\Validation\Error[] ...$rules
     * @return \Quanta\Validation\Traversed<T>
     */
    public static function merged(callable ...$rules): self
    {
        return new self(false, new Bound(...$rules));
    }

    /**
     * @param bool                                      $bound
     * @param callable(T): \Quanta\Validation\Error[]   $rule
     */
    public function __construct(bool $bound, callable $rule)
    {
        $this->bound = $bound;
        $this->rule = $rule;
    }

    /**
     * @param T[] $xs
     * @return \Quanta\Validation\Error[]
     */
    public function __invoke(array $xs): array
    {
        $errors = [];

        foreach ($xs as $key => $x) {
            $es = ($this->rule)($x);
            $es = array_map(fn ($e) => $e->nest((string) $key), $es);
            $es = array_values($es);

            if ($this->bound && count($es) > 0) {
                return $es;
            }

            $errors = [...$errors, ...$es];
        }

        return $errors;
    }
}
