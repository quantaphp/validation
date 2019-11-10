<?php

declare(strict_types=1);

namespace Quanta\Validation\PartialApplications;

use Quanta\Validation\InputInterface;

final class AppliedCallable
{
    /**
     * @var \Quanta\Validation\InputInterface
     */
    private $input;

    /**
     * @param \Quanta\Validation\InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\InputInterface
     */
    public function __invoke(InputInterface ...$inputs): InputInterface
    {
        return array_reduce($inputs, fn ($f, $x) => $x->apply($f), $this->input);
    }
}
