<?php

declare(strict_types=1);

namespace Quanta\Validation\PartialApplications;

use Quanta\Validation\Value;
use Quanta\Validation\Success;
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
            return new Success(new Value([]));
        }

        $x = reset($xs);
        $k = key($xs);

        unset($xs[$k]);

        $head = (new Success(new Value($x), (string) $k))->bind(...$this->fs);

        return $this->acc
            ? $this->consa((string) $k, $head, $xs)
            : $this->consm((string) $k, $head, $xs);
    }

    private function consa(string $k, InputInterface $head, array $xs): InputInterface
    {
        $cons = new MappedCallable(fn ($x, array $xs) => array_merge([$k => $x], $xs));

        return $cons($head, $this($xs));
    }

    private function consm(string $k, InputInterface $head, array $xs): InputInterface
    {
        return $head->bind(fn ($x) => $this($xs)->bind(fn ($xs) => array_merge([$k => $x], $xs)));
    }
}
