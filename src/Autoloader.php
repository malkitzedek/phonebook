<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

class Autoloader
{
    /**
     * @var string
     */
    private static $systemNsPrefix = 'Novostruev';

    /**
     * @var array
     */
    private static $baseDir = ['system' => 'src'];

    /**
     * Регистрирует функцию загрузки класса
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'loadClass']);
    }

    /**
     * @param $classname
     * @return bool
     */
    private static function loadClass(string $classname): bool
    {
        if (strpos($classname, self::$systemNsPrefix) === 0) {
            $unqualifiedClassname = self::getUnqualifiedClassname($classname);
            self::requireFile('system', $unqualifiedClassname);
            return true;
        }
        $unqualifiedClassname = str_replace('\\', DIRSEP, $classname);
        self::requireFile('vendor', $unqualifiedClassname);
        return true;
    }

    /**
     * @param string $classname
     * @return string
     */
    private static function getUnqualifiedClassname(string $classname): string
    {
        $classname = substr($classname, strlen(self::$systemNsPrefix));
        $classname = str_replace('\\', DIRSEP, $classname);
        return ltrim($classname, DIRSEP);
    }

    /**
     * @param string $classType
     * @param string $unqualifiedClassname https://www.php.net/manual/en/language.namespaces.basics.php
     * @return bool
     */
    private static function requireFile(string $classType, string $unqualifiedClassname): bool
    {
        $rootDir = dirname(dirname(__FILE__));
        $relativePath = self::getRelativePath($classType, $unqualifiedClassname);
        $absolutePath = "$rootDir/$relativePath";
        if (!file_exists($absolutePath)) {
            return false;
        }
        require_once($absolutePath);
        return true;
    }

    /**
     * @param string $classType
     * @param string $unqualifiedClassname
     * @return string
     */
    private static function getRelativePath(string $classType, string $unqualifiedClassname): string
    {
        if (!array_key_exists($classType, self::$baseDir)) {
            exit('Указан неверный тип класса');
        }
        return (self::$baseDir[$classType] . DIRSEP . $unqualifiedClassname . '.php');
    }
}
