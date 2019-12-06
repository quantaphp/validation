<?php

declare(strict_types=1);

namespace Quanta\Validation;

interface InputInterface
{
    /**
     * @return \Quanta\Validation\Success|\Quanta\Validation\Failure
     */
    public function result(): ResultInterface;

    /**
     * @param \Quanta\Validation\InputInterface ...$inputs
     * @return \Quanta\Validation\Data|\Quanta\Validation\Failure
     * @throws \InvalidArgumentException
     */
    public function merge(InputInterface ...$inputs): InputInterface;
}
