<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use PDO;
use ErrorException;

/**
 * Класс Data Base Interaction предназначен для взаимодействия с базой данных OJS
 */
class DB
{
    /**
     * @var PDO
     */
    private $PDO;

    /**
     * Количество строк, затронутых последним SQL-запросом
     * @var int
     */
    public $rowCount;

    /**
     * DBI constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->PDO = $pdo;
    }

    /**
     * Запускает подготовленный запрос на выполнение
     *
     * @param string $sql
     * @param array $values
     * @param bool $fetch
     * @param bool $fetchOneRow
     * @return mixed
     * @throws ErrorException
     */
    public function execute(string $sql, array $values, bool $fetch=false, bool $fetchOneRow=false)
    {
        $stmt = $this->PDO->prepare($sql);
        if (!$stmt->execute($values)) {
            throw new ErrorException('Запрос к БД не выполнен');
        }

        if ($fetch) {
            if ($fetchOneRow) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // ВАЖНО!!!
            // Если извлечь данные не удалось, то вернется пустой массив!
            return $result ?: [];
        }
        return 1;
    }

    /**
     * @param string $sql
     * @return mixed
     * @throws ErrorException
     */
    public function fetchAll(string $sql)
    {
        if (!($stmt = $this->PDO->query($sql))) {
            throw new ErrorException("Не удалось выполнить запрос к БД. Строка запроса: '{$sql}'");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->rowCount = $stmt->rowCount();

        return $rows;
    }

    /**
     * @param $sql
     * @return array|false
     * @throws ErrorException
     */
    public function fetch(string $sql)
    {
        if (!($stmt = $this->PDO->query($sql))) {
            throw new ErrorException("Не удалось выполнить запрос к БД. Строка запроса: {$sql}");
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает ID последней вставленной строки или значение последовательности
     *
     * @return mixed
     */
    public function lastInsertId(): int
    {
        return (int) $this->PDO->lastInsertId();
    }

    /**
     * @param $sql
     */
    public function exec($sql)
    {
        $this->PDO->exec($sql);
    }
}
