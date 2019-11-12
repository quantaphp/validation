<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class ArrayShape
{
    private $shape;

    public function __construct(array $shape)
    {
        $this->shape = $shape;
    }

    public function __invoke(array $data): InputInterface
    {
        $keys = array_keys($this->shape);
        $values = array_values($this->shape);

        $combine = Input::map(fn (...$xs) => array_combine($keys, $xs));

        $inputs = array_map(fn ($k, $fs) => (new ArrayKey($k))($data)->bind(...$fs), $keys, $values);

        return $combine(...$inputs);
    }
}
