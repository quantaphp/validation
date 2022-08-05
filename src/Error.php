<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class Error
{
    public static function from(string $template, mixed ...$xs): self
    {
        return new self(vsprintf($template, $xs));
    }

    /**
     * @var string[]
     */
    public readonly array $keys;

    private function __construct(public readonly string $message, string ...$keys)
    {
        $this->keys = $keys;
    }

    public function nest(string ...$keys): self
    {
        if (count($keys) == 0) return $this;

        $key = array_pop($keys);

        $error = new self($this->message, $key, ...$this->keys);

        return $error->nest(...$keys);
    }
}
