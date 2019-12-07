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

        $map = fn (string $key, $val) => $f($val)->bind(...$fs)->input($key);

        $init = new Data([]);
        $inputs = array_map($map, array_keys($xs), $xs);
        $reduce = fn ($merged, $input) => $merged->merge($input);

        return array_reduce($inputs, $reduce, $init)->result();
    }
}
