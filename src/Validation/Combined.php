<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Combined
{
    /**
     * @var Array<int, callable(array): (\Quanta\Validation\Success|\Quanta\Validation\Failure)>
     */
    private array $fs;

    /**
     * @param callable(array): (\Quanta\Validation\Success|\Quanta\Validation\Failure) ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(array $xs): InputInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        return $f == false ? new Success($xs) : $f($xs)->bind(...$fs);
    }
}
