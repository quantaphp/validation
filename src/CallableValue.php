<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class CallableValue implements ValueInterface
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @param mixed $f
     */
    public function __construct($f)
    {
        $this->f = $f;
    }

    /**
     * @inheritdoc
     */
    public function value()
    {
        return ($this->f)();
    }

    /**
     * Proxy the callable.
     *
     * @param mixed ...$xs
     * @return mixed
     */
    public function __invoke(...$xs)
    {
        return ($this->f)(...$xs);
    }
}
