<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Scope
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(array $xs): InputInterface
    {
        $x = $xs[$this->key] ?? [];

        return is_array($x)
            ? new Success($x, $this->key)
            : new Failure(new Error($this->key, 'must be an array', self::class));
    }
}
