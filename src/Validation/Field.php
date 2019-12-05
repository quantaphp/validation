<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Field
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var Array<int, callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure)>
     */
    private $fs;

    /**
     * @param string                                                                    $key
     * @param callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure)  ...$fs
     */
    public function __construct(string $key, callable ...$fs)
    {
        $this->key = $key;
        $this->fs = $fs;
    }

    /**
     * @param mixed[] $xs
     * @return \Quanta\Validation\Input|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        $fs = [...$this->fs];

        $f = array_shift($fs) ?? false;

        return $f == false
            ? new Input($xs)
            : $f($xs[$this->key])->bind(...$fs)->input($this->key);
    }
}
