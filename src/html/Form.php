<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\html;

class Form
{
    /**
     * @param array $attributes
     * @return string
     */
    public static function begin(array $attributes=[]): string
    {
        return HTML::tag('form', $attributes);
    }

    /**
     * @return mixed
     */
    public static function end(): string
    {
        return HTML::end('form');
    }

    /**
     * @param string $text
     * @param array $attributes
     * @return string
     */
    public static function label(string $text, array $attributes=[]): string
    {
        $html = HTML::tag('label', $attributes);
        $html .= $text;
        $html .= HTML::end('label');

        return $html;
    }

    /**
     * @param array $attributes
     * @return string
     */
    public static function input(array $attributes=[]): string
    {
        return HTML::tag('input', $attributes);
    }

    /**
     * @param array $attributes
     * @return string
     */
    public static function checkbox(array $attributes=[]): string
    {
        return HTML::tag('input', $attributes);
    }

    /**
     * @param array $attributes
     * @param array $optionValues
     * @param $selectedValue
     * @return string
     */
    public static function select(array $attributes=[], array $optionValues=[], string $selectedValue=null): string
    {
        $html = HTML::tag('select', $attributes);
        foreach ($optionValues as $value => $text ) {
            $selected = ((string) $value === $selectedValue) ? ' selected' : '';
            $html .= '<option value="' . $value . '"' . $selected . '>' . $text . '</option>';
        }
        $html .= HTML::end('select');

        return $html;
    }

    /**
     * @param string $text
     * @param array $attributes
     * @return string
     */
    public static function textarea(string $text, array $attributes=[]): string
    {
        $html = HTML::tag('textarea', $attributes);
        $html .= $text;
        $html .= HTML::end('textarea');

        return $html;
    }

    /**
     * @param string $label
     * @param string $element
     * @param string|null $class
     * @param bool $hidden
     * @return string
     */
    public static function group(
        string $label,
        string $element,
        string $class=null,
        bool $hidden=false
    ): string {
        $class = $class ? " {$class}" : '';

        $html = HTML::tag('div', ['class' => "form-group row{$class}", 'hidden' => $hidden]);
        $html .= $label;
        $html .= HTML::tag('div', ['class' => 'col-9']);
        $html .= $element;
        $html .= HTML::end('div');
        $html .= HTML::end('div');

        return $html;
    }

    /**
     * @param string $text
     * @param array $attributes
     * @return string
     */
    public static function button(string $text, array $attributes=[]): string
    {
        return HTML::tag('button', $attributes) . $text . HTML::end('button');
    }
}
