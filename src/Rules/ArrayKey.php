<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class ArrayKey
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var array<int, callable(mixed): \Quanta\Validation\InputInterface>
     */
    private $fs;

    /**
     * @param string                                                $key
     * @param callable(mixed): \Quanta\Validation\InputInterface    ...$fs
     */
    public function __construct(string $key, ...$fs)
    {
        $this->key = $key;
        $this->fs = $fs;
    }

    public function __invoke(array $x): InputInterface
    {
        if (! key_exists($this->key, $x)) {
            return new Failure(new Error(
                sprintf('key %s is required', $this->key),
                self::class,
                ['key' => $this->key],
            ));
        }

        return Input::key($this->key, ...$this->fs)($x);
    }
}
