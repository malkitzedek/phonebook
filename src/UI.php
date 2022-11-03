<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use Novostruev\html\Form;

class UI
{
    /**
     * @return string
     */
    public static function getSearchIcon(): string
    {
        return '<svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/><path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/></svg>';
    }

    /**
     * @return string
     */
    public static function addCss(): string
    {
        $links = [
            ["src" => "/lib/bootstrap/bootstrap.min.css"],
            ["src" => "/lib/select2/select2.min.css"],
            ["src" => "/lib/jquery-ui-1.12.1/jquery-ui.css"],
            ["src" => "/resources/css/custom.css"],
            ["src" => "//fonts.googleapis.com/css?family=Cuprum:regular,italic,700,700italic&amp;subset=cyrillic,latin"],
        ];

        $html = '';
        foreach ($links as $link) {
            $html .= '<link rel="stylesheet" href="' . $link['src'] . '">' . "\n";
        }
        return $html;
    }

    /**
     * @return string
     */
    public static function addJs(): string
    {
        $scripts = [
            ["src" => "/lib/jquery/jquery-3.5.1.min.js"],
            ["src" => "/lib/jquery-ui-1.12.1/jquery-ui.js"],
            ["src" => "/lib/jquery-validation-1.19.2/jquery.validate.min.js"],
            ["src" => "lib/jquery-validation-1.19.2/localization/messages_ru.js"],
            //["src" => "/lib/bootstrap/bootstrap.min.js"],
            ["src" => "/lib/select2/select2.min.js"],
            ["src" => "/resources/js/custom.js"],
        ];

        $html = '';
        foreach ($scripts as $script) {
            $html .= '<script src="' . $script['src'] . '"'
                . (isset($script['integrity'])
                    ? ' integrity="' . $script['integrity'] . '"' : '')
                . (isset($script['crossorigin']) ? ' crossorigin="' . $script['crossorigin'] . '"' : '')
                . '></script>' . "\n";
        }
        return $html;
    }

    /**
     * @param array $departments
     * @return string
     */
    public static function departmentSelect(array $departments)
    {
        $select = [
            'attributes' => [
                'class' => 'form-control',
                'id'    => 'department',
                'name'  => 'department',
            ],
        ];

        $optionValues = self::getOptionValues(
            $departments,
            'id',
            'human_name',
            'name'
        );
        return Form::select(
            $select['attributes'],
            (['Все подразделения'] + $optionValues),
            self::getDepartmentSelectedValue()
        );
    }

    /**
     * @return string|null
     */
    private static function getDepartmentSelectedValue(): ?string
    {
        return !empty($_GET['department']) ? $_GET['department'] : null;
    }

    /**
     * @param array $rows
     * @param string $keyName
     * @param string $valueName
     * @param string $safetyValueName
     * @return array
     */
    private static function getOptionValues(
        array $rows,
        string $keyName,
        string $valueName,
        string $safetyValueName=''
    ): array {
        return self::convertToOptionValues($rows, $keyName, $valueName, $safetyValueName);
    }

    /**
     * @param array $rows
     * @param string $keyName
     * @param string $valueName
     * @param string $safetyValueName
     * @return array
     */
    private static function convertToOptionValues(
        array $rows,
        string $keyName,
        string $valueName,
        string $safetyValueName=''
    ): array {
        $optionValues = [];
        if ($rows) {
            foreach ($rows as $row) {
                $key = $row[$keyName];
                if ($row[$valueName]) {
                    $optionValues[$key] = $row[$valueName];
                } else if ($safetyValueName) {
                    $optionValues[$key] = $row[$safetyValueName];
                }
            }
        }
        return $optionValues;
    }
}
