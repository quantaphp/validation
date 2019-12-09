<?php declare(strict_types=1);

namespace Quanta\Validation;

final class TraverseM
{
    /**
     * @var Array<int, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private array $fs;

    /**
     * @param callable(mixed): \Quanta\Validation\InputInterface ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Success<mixed[]>|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        if (count($xs) == 0 || $f == false) {
            return new Success($xs);
        }

        $val = reset($xs);
        $key = (string) key($xs);

        $xs = array_slice($xs, 1, null, true);

        return $f($val)->bind(...$fs)->nested($key)
            ->bind(fn ($head) => $this($xs)
            ->bind(fn (array $tail) => new Success(array_merge($head, $tail)))
        );
    }
}
