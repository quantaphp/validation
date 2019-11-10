<?php

declare(strict_types=1);

namespace Quanta\Validation\PartialApplications;

use Quanta\Validation\Success;
use Quanta\Validation\CallableValue;
use Quanta\Validation\InputInterface;

final class MappedCallable
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @param callable $f
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
    }

    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(InputInterface ...$inputs): InputInterface
    {
        return (new AppliedCallable(new Success(new CallableValue($this->f))))(...$inputs);
    }
}
