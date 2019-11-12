<?php

declare(strict_types=1);

namespace Quanta\Validation\PartialApplications;

use Quanta\Validation\Input;
use Quanta\Validation\Nested;
use Quanta\Validation\InputInterface;

final class TraversedCallable
{
    /**
     * @var bool
     */
    private $acc;

    /**
     * @var array<int, callable(mixed): InputInterface> $fs
     */
    private $fs;

    /**
     * @param bool                              $acc
     * @param callable(mixed): InputInterface   ...$fs
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
        if (count($xs) == 0) {
            return Input::unit([]);
        }

        $key = (string) key($xs);

        $head = (new Nested($key, ...$this->fs))($xs);

        unset($xs[$key]);

        return $this->acc
            ? $this->consa($key, $head, $xs)
            : $this->consm($key, $head, $xs);
    }

    private function consa(string $k, InputInterface $head, array $xs): InputInterface
    {
        $cons = Input::map(fn ($x, array $xs) => array_merge([$k => $x], $xs));

        return $cons($head, $this($xs));
    }

    private function consm(string $k, InputInterface $head, array $xs): InputInterface
    {
        return $head->bind(fn ($x) => $this($xs)->bind(fn ($xs) => array_merge([$k => $x], $xs)));
    }
}
