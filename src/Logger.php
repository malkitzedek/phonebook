<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

class Logger
{
    const DEFAULT_LOG_DIR = 'logs';

    const DEFAULT_FILENAME = 'log.txt';

    /**
     * @var string Путь к корневому каталогу файлов Мудла
     */
    private static $rootDir;

    /**
     * Logger constructor.
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        self::$rootDir = $rootDir;

        $this->checkLogDir();
    }

    /**
     * Создает каталог для хранения логов, если он не существует
     */
    private function checkLogDir(): void
    {
        if (!file_exists(self::$rootDir . DIRSEP . self::DEFAULT_LOG_DIR)) {
            mkdir(self::$rootDir . DIRSEP . self::DEFAULT_LOG_DIR, 0777, true);
        }
    }

    /**
     * Метод пишет логи в файл на диске
     * В случае если лог-файла на диске не существует,
     * он автоматически создаётся функцией fopen(),
     * а в случае невозможности создать файл, генерируется ошибка
     *
     * @param string $text
     * @param string|null $filename Name of log file
     *
     * @return void - пустое значение
     */
    public function log(string $text, string $filename=null)
    {
        $fh = fopen($this->getFilename($filename), 'a');
        flock($fh, LOCK_EX);
        fwrite($fh, $this->getContent($text));
        flock($fh, LOCK_UN);
        fclose($fh);
    }

    /**
     * @param string|null $filename
     * @return string
     */
    private function getFilename(string $filename=null)
    {
        $filename = $filename ?: self::DEFAULT_FILENAME;
        return self::$rootDir . DIRSEP . self::DEFAULT_LOG_DIR . DIRSEP . $filename;
    }

    /**
     * @param string $text
     * @return string
     */
    private function getContent(string $text): string
    {
        return date('Y-m-d H:i:s') . ": " . $text . "\n";
    }
}
