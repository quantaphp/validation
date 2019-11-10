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

        $combine = Input::map(fn (...$xs) => array_combine($keys, $xs));

        $inputs = [];

        foreach ($this->shape as $key => $fs) {
            $inputs[] = (new OnKey($key))($data)->bind(...$fs);
        }

        return $combine(...$inputs);
    }
}
