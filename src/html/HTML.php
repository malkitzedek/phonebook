<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\html;

class HTML
{
    private static $booleanAttribute = ['required', 'disabled', 'checked', 'hidden'];

    /**
     * @param string $tagName
     * @param array $attributes
     * @return string
     */
    public static function tag(string $tagName, array $attributes=[]): string
    {
        $html = "<{$tagName}";

        if (empty($attributes)) {
            return "{$html}>";
        }

        foreach ($attributes as $key => $value) {
            if (!$value) {
                continue;
            }

            if (in_array($key, self::$booleanAttribute) && $value === true) {
                $html .= ' ' . $key;
                continue;
            }
            $html .= ' ' . $key .'="' . $value . '"';
        }
        return "{$html}>";
    }

    /**
     * @param string $tagName
     * @return mixed
     */
    public static function end(string $tagName): string
    {
        return str_replace('<', '</', self::tag($tagName));
    }

    /**
     * @return string[]
     */
    public function getBooleanAttribute(): array
    {
        return self::$booleanAttribute;
    }
}
