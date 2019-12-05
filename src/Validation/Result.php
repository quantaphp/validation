<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Result
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
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): ResultInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        return $f == false
            ? new Success($xs)
            : $f($xs)->bind(...$fs)->result();
    }
}
