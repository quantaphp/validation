<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class ErrorFormatter implements ErrorFormatterInterface
{
    public function __invoke(Error $error): string
    {
        if (count($error->keys) == 0) {
            return $error->message;
        }

        $keys = [...$error->keys];
        $message = $error->message;

        if (strpos($error->message, '%s') !== false) {
            $key = array_pop($keys);

            $message = sprintf($message, $key);
        }

        if (count($keys) == 0) {
            return $message;
        }

        $path = implode('', array_map(fn (string $k) => '[' . $k . ']', $keys));

        return implode(' ', [$path, $message]);
    }
}
