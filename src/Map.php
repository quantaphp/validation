<?php declare(strict_types=1);

namespace Quanta\Validation;

final class Map
{
    /**
     * @var boolean
     */
    private bool $bound;

    /**
     * @var callable(mixed): mixed
     */
    private $rule;

    /**
     * @param callable(mixed): mixed ...$rules
     * @return \Quanta\Validation\Map
     */
    public static function bound(callable ...$rules): self
    {
        return new self(true, new Bound(...$rules));
    }

    /**
     * @param callable(mixed): mixed ...$rules
     * @return \Quanta\Validation\Map
     */
    public static function merged(callable ...$rules): self
    {
        return new self(false, new Bound(...$rules));
    }

    /**
     * @param bool                      $bound
     * @param callable(mixed): mixed    $rule
     */
    public function __construct(bool $bound, callable $rule)
    {
        $this->bound = $bound;
        $this->rule = $rule;
    }

    /**
     * @param mixed[] $xs
     * @return mixed[]
     * @throws \Quanta\Validation\InvalidDataException
     */
    public function __invoke(array $xs): array
    {
        $errors = [];

        foreach ($xs as $key => $x) {
            try {
                $xs[$key] = ($this->rule)($x);
            }

            catch (InvalidDataException $e) {
                $e->nest((string) $key);

                if ($this->bound) {
                    throw $e;
                }

                $errors = [...$errors, ...$e->errors()];
            }
        }

        if (count($errors) == 0) {
            return $xs;
        }

        throw new InvalidDataException(...$errors);
    }
}
