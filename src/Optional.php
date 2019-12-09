<?php

declare(strict_types=1);

namespace Quanta\Validation;

/**
 * @template T
 */
final class Optional
{
    /**
     * @var T
     */
    private $x;

    /**
     * @param T $x
     */
    public function __construct($x)
    {
        $this->x = $x;
    }

    /**
     * @param string $key
     * @return \Quanta\Validation\Success<array<string, T>>
     */
    public function __invoke(string $key): InputInterface
    {
        return new Success([$key => $this->x]);
    }
}
