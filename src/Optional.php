<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Optional
{
    /**
     * @var mixed
     */
    private $x;

    /**
     * @param mixed $x
     */
    public function __construct($x)
    {
        $this->x = $x;
    }

    /**
     * @param string $key
     * @return \Quanta\Validation\Data
     */
    public function __invoke(string $key): InputInterface
    {
        return new Data([$key => $this->x]);
    }
}
