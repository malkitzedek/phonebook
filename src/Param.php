<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

class Param
{
    /**
     * @param string $name
     * @return string|null
     */
    public static function get(string $name): ?string
    {
        return !empty($_GET[$name]) ? $_GET[$name] : null;
    }
}
