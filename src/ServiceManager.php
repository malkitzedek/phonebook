<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use ErrorException;
use Novostruev\services\{DepartmentService, Service, StaffService};

/**
 * Класс предназначен для управления сервисами
 */
class ServiceManager
{
    const CONFIG_NAME = 'service_parameters';

    /**
     * @var array
     */
    private static $parameters = [];

    /**
     * @param string $name
     * @return Service
     * @throws ErrorException
     */
    public static function getService(string $name): Service
    {
        self::init();

        $params = self::getServiceParameters($name);
        switch ($name) {
            case 'staff':
                return new StaffService($params, new GeneralRequest());
            case 'department':
                return new DepartmentService($params, new GeneralRequest());
            default:
                throw new ErrorException('Класс сервиса не определён');
        }
    }

    /**
     * @throws ErrorException
     */
    private static function init()
    {
        if (empty(self::$parameters)) {
            self::$parameters = Config::get(self::CONFIG_NAME);
        }
    }

    /**
     * @param string $serviceName
     * @return array
     * @throws ErrorException
     */
    private static function getServiceParameters(string $serviceName): array
    {
        if (empty(self::$parameters[$serviceName])) {
            throw new ErrorException('Соответствующие сервису параметры не определены');
        }
        return self::$parameters[$serviceName];
    }
}
