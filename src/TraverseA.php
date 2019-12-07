<?php declare(strict_types=1);

namespace Quanta\Validation;

final class TraverseA
{
    /**
     * @var Array<int, callable(mixed): \Quanta\Validation\ResultInterface>
     */
    private array $fs;

    /**
     * @param callable(mixed): \Quanta\Validation\ResultInterface ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): ResultInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        if (count($xs) == 0 || $f == false) {
            return new Success($xs);
        }

        $inputs = array_map(function (string $key, $val) use ($f, $fs) {
            return $f($val)->bind(...$fs)->input($key);
        }, array_keys($xs), $xs);

        return array_shift($inputs)->merge(...$inputs)->result();
    }
}
