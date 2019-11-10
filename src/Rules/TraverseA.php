<?php

declare(strict_types=1);

namespace Quanta\Validation\Rules;

use Quanta\Validation\Input;
use Quanta\Validation\Success;
use Quanta\Validation\InputInterface;

final class TraverseA
{
    private $fs;

    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    public function __invoke(array $xs): InputInterface
    {
        if (count($xs) == 0) {
            return Input::unit([]);
        }

        $x = reset($xs);
        $k = key($xs);

        unset($xs[$k]);

        $cons = Input::map(fn ($x, array $xs) => array_merge([$x], $xs));

        $head = (new Success($x, (string) $k))->bind(...$this->fs);
        $tail = $this($xs);

        return $cons($head, $tail);
    }
}
