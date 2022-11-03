<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use stdClass;
use ReflectionObject;
use ReflectionException;

class Helper
{
    /**
     * Разрешает доступ к справочнику
     * только для определённых IP
     *
     * IP-адреса локальной сети университета:
     * 192.168.*.*
     * 94.181.117.*
     * 10.0.*.*
     * 172.16.*.*
     *
     */
    public static function checkIP(): void
    {
        $digits = explode('.', $_SERVER['REMOTE_ADDR']);
        $digit1 = (int) $digits[0];
        $digit2 = (int) $digits[1];
        $digit3 = (int) $digits[2];

        if (
            ($digit1 === 192 && $digit2 === 168)
            || ($digit1 === 94 && $digit2 === 181 && $digit3 === 117)
            || ($digit1 === 10 && $digit2 === 0)
            || ($digit1 === 172 && $digit2 === 16)
        ) {
            return;
        }
        echo 'Доступ запрещён';
        exit();
    }

    /**
     * @param string $str
     * @return string
     */
    public static function mbUcfirst(string $str): string
    {
        $firstCharacter = mb_strtoupper(mb_substr($str, 0, 1));
        return $firstCharacter . mb_substr($str, 1);
    }

    /**
     * @param string $str
     * @return string
     */
    public static function getUcFirstCharacter(string $str): string
    {
        return mb_strtoupper(mb_substr($str, 0, 1));
    }

    /**
     * @param string $str
     * @return string
     */
    public static function normalizeString(string $str): string
    {
        return self::getUcFirstCharacter($str) . mb_substr(mb_strtolower($str), 1);
    }

    /**
     * @param $str
     * @return int
     */
    public static function countWords($str): int
    {
        $str = mb_ereg_replace('\s+', ' ', trim($str));
        $words = explode(' ', $str);
        return count($words);
    }

    /**
     * Функция для отладки
     *
     * @param $var
     * @param bool $exit
     */
    public static function dump($var, bool $exit=false)
    {
        echo '<br>';
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        echo '<br>';

        if ($exit) {
            exit();
        }
    }

    /**
     * Удаляет пустые ряды
     *
     * @param array $array
     * @return array
     */
    public static function removeEmptyRows(array $array): array
    {
        foreach ($array as $key => $values) {
            $cnt = 0;
            $length = count($values);

            foreach ($values as $value) {
                if ($value === null) {
                    $cnt++;
                }
            }

            if ($cnt === $length) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * @param stdClass $object
     * @param array $requiredFields
     * @return bool
     */
    public static function requiredFieldsExists(stdClass $object, array $requiredFields): bool
    {
        $properties = (new ReflectionObject($object))->getProperties();
        foreach ($properties as $property) {
            if (!in_array($property->getName(), $requiredFields)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param stdClass $object
     * @param array $fieldAliases
     * @return array
     * @throws ReflectionException
     */
    public static function getRelatedValues(stdClass $object, array $fieldAliases)
    {
        $values = [];
        foreach ($fieldAliases as $alias) {
            $property = (new ReflectionObject($object))->getProperty($alias);
            $values[] = $property->getValue($object);
        }
        return $values;
    }
}




