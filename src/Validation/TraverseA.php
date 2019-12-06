<?php declare(strict_types=1);

namespace Quanta\Validation;

final class TraverseA
{
    /**
     * @var Array<int, callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure)>
     */
    private array $fs;

    /**
     * @param callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure) ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Data|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        if (count($xs) == 0 || $f == false) {
            return new Data($xs);
        }

        $val = reset($xs);
        $key = (string) key($xs);

        $xs = array_slice($xs, 1, null, true);

        return $f($val)->bind(...$fs)->input($key)->merge($this($xs));
    }
}
