<?php

declare(strict_types=1);

namespace Common\Tool;

class Censor
{
    /**
     * @param mixed $value
     * @return string|null
     */
    public static function censure($value): ?string
    {
        if ($value === null) {
            return $value;
        }

        $string = (string)$value;

        return preg_replace('/\p{L}|\d/u', '█', $string);
    }
}
