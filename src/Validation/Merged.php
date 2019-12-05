<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Merged
{
    /**
     * @var Array<int, callable(mixed[]): (\Quanta\Validation\Input|\Quanta\Validation\Failure)>
     */
    private array $fs;

    /**
     * @param callable(mixed[]): (\Quanta\Validation\Input|\Quanta\Validation\Failure) ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Input|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        $inputs = array_map(fn ($f) => $f($xs), $this->fs);

        $input = array_shift($inputs) ?? false;

        return $input == false ? new Success($xs) : $input->merge(...$inputs);
    }
}
