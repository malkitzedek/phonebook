<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use PDO;
use ErrorException;
use Novostruev\phonebook\Phonebook;
use PHPMailer\PHPMailer\PHPMailer;

class App
{
    /**
     * Имя конфигурационного файлы приложиения
     */
    const CONFIG_NAME = 'settings';

    /**
     * @var DB Объект для взаимодействия с базой данных
     */
    public static $db;

    /**
     * @var Phonebook
     */
    private $phonebook;

    /**
     * App constructor.
     * @param string|null $phonebookType
     */
    public function __construct(string $phonebookType='')
    {
        try {
            $config = Config::get(self::CONFIG_NAME);
        } catch (ErrorException $e) {
            exit($e->getMessage());
        }

        self::$db = new DB(new PDO(
            $this->getDSN($config),
            $config['db']['user'],
            $config['db']['password']
        ));

        $this->setPhonebook($phonebookType);
    }

    /**
     * @param array $config
     * @return string
     */
    private function getDSN(array $config):string
    {
        return "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset=utf8";
    }

    /**
     * @param string $phonebookType
     */
    private function setPhonebook(string $phonebookType): void
    {
        $this->phonebook = PhonebookManager::getInstance($phonebookType);
    }

    /**
     * Выводит телефонный справочник
     */
    public function run(): void
    {
        $this->phonebook->output();
    }

    /**
     * Обновляет данные телефонного справочника
     */
    public function actualize(): void
    {
        $this->phonebook->actualize();
    }

    /**
     * @param PHPMailer $mail
     * @return PHPMailer
     * @throws ErrorException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function configureMail(PHPMailer $mail): PHPMailer
    {
        $config = Config::get('settings');
        if (empty($config['mail'])) {
            throw new ErrorException('Mail settings not defined');
        }
        $mailConfig = $config['mail'];

        $mail->CharSet = 'UTF-8';

        //Server settings
        $mail->isSMTP();
        $mail->SMTPAuth     = true;
        $mail->SMTPSecure   = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Host         = $mailConfig['host'];
        $mail->Username     = $mailConfig['username'];
        $mail->Password     = $mailConfig['password'];
        $mail->Port         = $mailConfig['port'];

        $mail->setFrom($mailConfig['username'], 'Телефонный справочник ИжГТУ');
        $mail->addAddress($mailConfig['recipient_email']);

        return $mail;
    }

    /**
     * Экспортирует данные телефонного справочника в Excel-файл
     */
    public function exportToExcelFile(): void
    {
        try {
            Export::toExcel();
        } catch (ErrorException $e) {
            exit($e->getMessage());
        }
    }
}
