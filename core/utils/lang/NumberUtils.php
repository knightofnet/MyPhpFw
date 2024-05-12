<?php

namespace myphpfw\core\utils\lang;

class NumberUtils
{

    private function __construct()
    {
    }

    /**
     * Essaie de parser une valeur en entier
     *
     * @param $value
     * @param int|null $default
     * @return int
     */
    public static function tryParseInt($value, ?int $default = 0)
    {
        return is_numeric($value) ? (int)$value : $default;
    }

    /**
     * Essaie de parser une valeur en réel
     *
     * @param $value
     * @param float|null $default
     * @return float
     */
    public static function tryParseFloat($value, ?float $default = 0)
    {
        return is_numeric($value) ? (float)$value : $default;
    }

}