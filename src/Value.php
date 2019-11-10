<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Value implements ValueInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function value()
    {
        return $this->value;
    }
}
