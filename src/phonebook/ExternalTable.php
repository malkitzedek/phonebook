<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\phonebook;

use Novostruev\Helper;

class ExternalTable extends Table
{
    /**
     * @var string[][] Описание столбцов заголовка таблицы
     */
    private static $tableHeadColumns = [
        ['title' => 'Должность',        'width' => '27%',],
        ['title' => 'ФИО',              'width' => '29%',],
        ['title' => 'Внутр. #',         'width' => '8%',],
        ['title' => 'Прямой #',         'width' => '9%',],
        ['title' => 'Email',            'width' => '10%',],
        ['title' => 'Расположение',     'width' => '17%',],
    ];

    /**
     * @param array $data
     * @return string
     */
    public static function generate(array $data): string
    {
        $html = '<table class="table table-hover table-bordered">';
        $html .= '<thead>';
        $html .= self::generateTableHeadContent();
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= self::generateTableBodyContent($data);
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }

    /**
     * @return string
     */
    private static function generateTableHeadContent(): string
    {
        $html = '<tr class="text-center">';
        foreach (self::$tableHeadColumns as $column) {
            $html .= '<th scope="col" style="width: ' . $column['width'] . ';">' . $column['title'] . '</th>';
        }
        $html .= '</tr>';
        return $html;
    }

    /**
     * РЕКУРСИВНАЯ ФУНКЦИЯ!!!
     *
     * @param array $data
     * @return string
     */
    private static function generateTableBodyContent(array $data): string
    {
        $html = '';
        foreach ($data as $key => $row) {
            // Выводим только те подразделения,
            // у которых есть записи справочника или дочерние подразделения
            if (!empty($row['entries']) || !empty($row['child_departments'])) {
                $html .= self::getDepartmentName($row);
            }

            if (!empty($row['entries'])) {
                foreach ($row['entries'] as $entry) {
                    $html .= '<tr>';
                    $html .= self::getPost($entry);
                    $html .= self::getStaffFullname($entry);
                    $html .= self::getInternalPhoneNumber($entry);
                    $html .= self::getDirectPhoneNumber($entry);
                    $html .= self::getEmail($entry);
                    $html .= self::getLocation($entry);
                    $html .= '</tr>';
                }
            }

            if (!empty($row['child_departments'])) {
                $html .= self::generateTableBodyContent($row['child_departments']);
            }
        }
        return $html;
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getDepartmentName(array $entry): string
    {
        if ($entry['name'] === 'Ректор') {
            return '<tr class="department-title"><td colspan="6" class="text-center">'
            . self::generateStaffLink(150, 'Ректор') . '</td></tr>';
        }

        $content = $name = $entry['human_name'] ?: $entry['name'];
        if ($id = $entry['istu_id']) {
            $content = self::generateDepartmentNameLink($id, $name);
        }
        return '<tr class="department-title"><td colspan="6" class="text-center">'
            . $content . '</td></tr>';
    }

    /**
     * @param int $id
     * @param string $name
     * @return string
     */
    private static function generateDepartmentNameLink(int $id, string $name): string
    {
        return '<a href="https://istu.ru/department/' . $id
            . '" target="_blank">' . $name . '</a>';
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getPost(array $entry): string
    {
        return '<td>' . Helper::normalizeString($entry['post']) . '</td>';
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getStaffFullname(array $entry): string
    {
        $content = $fullName = $entry['staff_fullname'];
        if ($id = $entry['staff_istu_id']) {
            $content = self::generateStaffLink($id, $fullName);
        }
        return '<td class="staff-fullname">' . $content . '</td>';
    }

    /**
     * @param int $id
     * @param string $fullName
     * @return string
     */
    private static function generateStaffLink(int $id, string $fullName): string
    {
        return '<a href="https://istu.ru/staff/' . $id
            . '" target="_blank">' . $fullName . '</a>';
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getInternalPhoneNumber(array $entry): string
    {
        $singlePhoneNumber = '83412776055';
        $cssClasses = 'internal-phone-number text-center font-weight-bold';

        $phoneNumber = $entry['internal_phone_number'];
        $phoneNumber = self::generatePhoneNumberLink(
            $singlePhoneNumber,
            $phoneNumber,
            $phoneNumber
        );
        return '<td class="' . $cssClasses . '">' . $phoneNumber . '</td>';
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getDirectPhoneNumber(array $entry): string
    {
        if ($phoneNumber = $entry['direct_phone_number']) {
            $phoneNumber = self::generatePhoneNumberLink(
                $phoneNumber,
                self::formatPhoneNumber($phoneNumber)
            );
        }
        return '<td class="direct-phone-number text-center">' . $phoneNumber . '</td>';
    }

    /**
     * @param string $phoneNumber
     * @param string $formattedPhoneNumber
     * @param string $extension
     * @return string
     */
    private static function generatePhoneNumberLink(
        string $phoneNumber,
        string $formattedPhoneNumber,
        string $extension=''
    ): string {
        $tel = $phoneNumber . ($extension ? (",$extension") : '');
        return '<a href="tel:' . $tel . '">' . $formattedPhoneNumber . '</a>';
    }

    /**
     * Приводит номер телефона к человекопонятному виду
     *
     * @param string $phoneNumber
     * @return string
     */
    private static function formatPhoneNumber(string $phoneNumber): string
    {
        return (strlen($phoneNumber) === 6) ? self::hyphenate($phoneNumber) : $phoneNumber;
    }

    /**
     * @param $str
     * @return string
     */
    public static function hyphenate($str) {
        return implode("-", str_split($str, 2));
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getEmail(array $entry): string
    {
        return '<td class="text-center">' . $entry['email'] . '</td>';
    }

    /**
     * @param array $entry
     * @return string
     */
    private static function getLocation(array $entry): string
    {
        return '<td class="text-center">' . $entry['location'] . '</td>';
    }
}
