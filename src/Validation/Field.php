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
     * @var callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure)
     */
    private $f;

    /**
     * @param string                                                                    $key
     * @param callable(mixed): (\Quanta\Validation\Success|\Quanta\Validation\Failure)  $f
     */
    public function __construct(string $key, callable $f)
    {
        $this->key = $key;
        $this->f = $f;
    }

    /**
     * @param array $xs
     * @return \Quanta\Validation\Input|\Quanta\Validation\Failure
     */
    public function __invoke(array $xs): InputInterface
    {
        return ($this->f)($xs[$this->key])->extract(
            fn ($x) => new Input([$this->key => $x]),
            fn (...$errors) => new Failure(...array_map(function ($error) {
                return new NestedError($this->key, $error);
            }, $errors)),
        );
    }
}
