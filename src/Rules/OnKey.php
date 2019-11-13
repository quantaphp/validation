<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class OnKey
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __invoke(array $data): InputInterface
    {
        return Input::unit($data[$this->key]);
    }
}
