<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Merged
{
    /**
     * @var Array<int, callable(array): (\Quanta\Validation\Input|\Quanta\Validation\Failure)>
     */
    private array $fs;

    /**
     * @param callable(array): (\Quanta\Validation\Input|\Quanta\Validation\Failure) ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param array $xs
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): ResultInterface
    {
        $inputs = array_map(fn ($f) => $f($xs), $this->fs);

        $input = array_shift($inputs) ?? false;

        if ($input == false) {
            return new Success($xs);
        }

        return $input->merge(...$inputs)->extract(
            fn (array $xs) => new Success($xs),
            fn (...$errors) => new Failure(...$errors),
        );
    }
}
