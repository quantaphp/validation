<?php

declare(strict_types=1);

namespace Quanta\Validation\PartialApplications;

use Quanta\Validation\InputInterface;

final class BoundCallable
{
    /**
     * @var array<int, callable(mixed): InputInterface> $fs
     */
    private $fs;

    /**
     * @param callable(mixed): InputInterface ...$fs
     */
    public function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param \Quanta\Validation\InputInterface $input
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(InputInterface $input): InputInterface
    {
        return $input->bind(...$this->fs);
    }
}
