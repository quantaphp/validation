<?php

declare(strict_types=1);

namespace Quanta\Validation;

final class ErrorFormatter implements ErrorFormatterInterface
{
    public function __invoke(Error $error): string
    {
        $keys = $error->keys();
        $default = $error->default();
        $params = array_values($error->params());

        $message = vsprintf($default, $params);

        if (strpos($message, '{key}') !== false) {
            $key = count($keys) == 0 ? 'value' : '[' . array_pop($keys) . ']';

            $message = str_replace('{key}', $key, $message);
        }

        if (count($keys) > 0) {
            $path = implode('', array_map(fn (string $k) => '[' . $k . ']', $keys));

            $message = implode(' ', [$path, $message]);
        }

        return $message;
    }
}
