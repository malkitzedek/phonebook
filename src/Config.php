<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use ErrorException;

class Config
{
    /**
     * Каталог хранения конфигурационных файлов приложения
     */
    const CONFIG_DIR = 'configs';

    /**
     * @param string $configName
     * @return array
     * @throws ErrorException
     */
    public static function get(string $configName): array
    {
        $configFileName = self::getFilename($configName);
        if (!file_exists($configFileName)) {
            throw new ErrorException("Configuration file $configName.php not found");
        }

        // ВАЖНО!!!
        // Использовать обязательно require, а не require_once,
        // чтобы обеспечить возможность многократного подлкючения
        // одного и того же конфигурационного файла
        $config = require($configFileName);
        if (!is_array($config)) {
            throw new ErrorException('Config file data is not an array');
        }
        return $config;
    }

    /**
     * @param string $configName
     * @return string
     */
    public static function getFilename(string $configName): string
    {
        $rootDir = dirname(__DIR__);

        return $rootDir . DIRSEP . self::CONFIG_DIR . DIRSEP . $configName . '.php';
    }
}
