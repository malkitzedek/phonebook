<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\phonebook;

use SoapFault;
use SoapClient;
use ErrorException;
use Novostruev\Config;

class DataReceive1cService
{
    const CONFIG_NAME = '1c_service_parameters';

    /**
     * @var array
     */
    private static $config = null;

    /**
     * @var string
     */
    private static $url = null;

    /**
     * @var array
     */
    private static $params = null;

    /**
     * @var SoapClient
     */
    private static $client = null;

    /**
     * Инициализирует конфигурационные параметры сервиса 1C
     * и создаёт Soap-клиент
     *
     */
    private static function init(): void
    {
        try {
            if (empty(self::$config)) {
                self::$config = Config::get(self::CONFIG_NAME);
            }
        } catch (ErrorException $e) {
            exit($e->getMessage());
        }

        self::setUrl();
        self::setParams();

        try {
            self::setClient();
        } catch (SoapFault $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     *
     */
    private static function setUrl(): void
    {
        if (empty(self::$url)) {
            self::$url = self::$config['url'];
        }
    }

    /**
     *
     */
    private static function setParams(): void
    {
        if (empty(self::$params)) {
            self::$params = [
                'login'         => self::$config['login'],
                'password'      => self::$config['password'],
                "exceptions"    => true,
                "trace"         => true,
            ];
        }
    }

    /**
     * Создаёт Soap-клиент
     *
     * @throws SoapFault
     */
    private static function setClient(): void
    {
        if (empty(self::$client)) {
            self::$client = new SoapClient(self::$url, self::$params);
        }
    }

    /**
     * @param string $type
     * @return array
     * @throws ErrorException
     */
    public static function getData(string $type): array
    {
        self::init();

        switch ($type) {
            case 'entry':
                return json_decode((self::$client->GetInformationPhoneNumbers())->return);
            case 'department':
                return json_decode((self::$client->GetDepartmentInfo())->return);
            default:
                throw new ErrorException('1C data type not defined');
        }
    }
}
