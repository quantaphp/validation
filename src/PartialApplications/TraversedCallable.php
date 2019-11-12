<?php

declare(strict_types=1);

namespace Quanta\Validation\PartialApplications;

use Quanta\Validation\Named;
use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

final class TraversedCallable
{
    /**
     * @var bool
     */
    private $acc;

    /**
     * @var array<int, callable(string, mixed): InputInterface> $fs
     */
    private $fs;

    /**
     * @param bool                                      $acc
     * @param callable(string, mixed): InputInterface   ...$fs
     */
    public function __construct(bool $acc, callable ...$fs)
    {
        $this->acc = $acc;
        $this->fs = $fs;
    }

    /**
     * @param array $xs
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(array $xs): InputInterface
    {
        $init = Input::unit([]);
        $values = array_map(fn ($k, $v) => [(string) $k, $v], array_keys($xs), $xs);
        $reduce = [$this, 'reduce'];

        return array_reduce($values, $reduce, $init);
    }

    private function reduce(InputInterface $tail, array $tuple): InputInterface
    {
        [$key, $value] = $tuple;

        $fs = array_map(fn ($f) => fn ($x) => $f($key, $x), $this->fs);

        $cons = fn ($x, array $xs) => array_merge($xs, [$key => $x]);

        $head = Input::unit($value)->bind(...$fs);

        return $this->acc
            ? $this->consa($head, $tail, $cons)
            : $this->consm($head, $tail, $cons);
    }

    private function consa(InputInterface $head, InputInterface $tail, callable $cons): InputInterface
    {
        return Input::map($cons)($head, $tail);
    }

    private function consm(InputInterface $head, InputInterface $tail, callable $cons): InputInterface
    {
        return $head->bind(fn ($x) => $tail->bind(fn ($xs) => $cons($x, $xs)));
    }
}
