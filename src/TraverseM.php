<?php declare(strict_types=1);

namespace Quanta\Validation;

final class TraverseM
{
    /**
     * @var null|callable(mixed): \Quanta\Validation\InputInterface
     */
    private $f;

    /**
     * @var Array<int, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private array $fs;

    /**
     * @param null|callable(mixed): \Quanta\Validation\InputInterface   $f
     * @param callable(mixed): \Quanta\Validation\InputInterface        ...$fs
     */
    public function __construct(callable $f = null, callable ...$fs)
    {
        $this->f = $f;
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Success<mixed[]>|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        if (count($xs) == 0 || is_null($this->f)) {
            return new Success($xs);
        }

        $val = reset($xs);
        $key = (string) key($xs);

        $xs = array_slice($xs, 1, null, true);

        return ($this->f)($val)->bind(...$this->fs)->nested($key)
            ->bind(fn (array $head) => $this($xs)
            ->bind(fn (array $tail) => new Success(array_merge($head, $tail)))
        );
    }
}
