<?php declare(strict_types=1);

namespace Quanta\Validation;

final class TraverseA
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
        /** for phpstan */
        $f = $this->f;

        if (count($xs) == 0 || is_null($f)) {
            return new Success($xs);
        }

        $inputs = array_map(function (string $key, $val) use ($f) {
            return $f($val)->bind(...$this->fs)->nested($key);
        }, array_keys($xs), $xs);

        return array_shift($inputs)->merge(...$inputs);
    }
}
