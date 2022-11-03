<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\phonebook;

use Exception;
use ErrorException;
use Novostruev\DB;
use Novostruev\Param;
use Novostruev\model\Model;
use Novostruev\ServiceManager;
use Novostruev\services\Service;

class Phonebook
{
    const VIEW_FILENAME = 'default.php';

    /**
     * @var DB Объект для взаимодействия с базой данных
     */
    public static $db;

    /**
     * Phonebook constructor.
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        self::$db = $db;
    }

    /**
     * Выводит html-страницу
     */
    public function output(): void
    {
        $this->outputHook();

        $searchParam     = Param::get('search');
        $departmentParam = Param::get('department');

        $data = $this->getData([
            'search'     => $searchParam,
            'department' => $departmentParam
        ]);
        $allDepartments = $this->getAllDepartments();

        require_once('views/' . static::VIEW_FILENAME);
    }

    /**
     * Хук (крючок, на который можно навесить код)
     */
    protected function outputHook(){}

    /**
     * @param array $params
     * @return array
     */
    protected function getData(array $params): array
    {
        try {
            if ($params['search']) {
                return $this->getDataBySearch($params['search']);
            } elseif ($params['department']) {
                return $this->getDataByDepartmentId($params['department']);
            } else {
                return $this->collectData($this->getMainDepartments());
            }
        } catch (ErrorException $e) {
            exit('Возникла ошибка при извлечении записей справочника');
        }
    }

    /**
     * @return array
     */
    protected function getAllDepartments(): array
    {
        try {
            return $this->getNotMainDepartments('d.human_name, d.name');
        } catch (ErrorException $e) {
            exit('Не удалось извлечь подразделения');
        }
    }

    /**
     * @param string $search
     * @return array
     * @throws ErrorException
     */
    public function getDataBySearch(string $search)
    {
        $entries = $this->findEntries($search);
        $departmentCodes = $this->extractDepartmentCodes($entries);
        $departments = $this->getDepartmentsByCodes($departmentCodes);
        $departmentTree = $this->buildDepartmentTree($departments);
        return $this->bindEntriesWithDepartments($entries, $departmentTree);
    }

    /**
     * @param string $search
     * @return array
     * @throws ErrorException
     */
    public function getDataBySearch_old(string $search)
    {
        $entries = $this->findEntries($search);
        $departmentIds = $this->extractDepartmentIds($entries);
        $departments = $this->getDepartments($departmentIds);
        $departmentTree = $this->buildDepartmentTree($departments);
        return $this->bindEntriesWithDepartments($entries, $departmentTree);
    }

    /**
     * @param array $entries
     * @return array
     */
    public function extractDepartmentCodes(array $entries): array
    {
        $codes = [];
        foreach ($entries as $entry) {
            $codes[] = $entry['department_code'];
        }
        return array_unique($codes);
    }

    /**
     * @param array $entries
     * @return array
     */
    public function extractDepartmentIds(array $entries): array
    {
        $ids = [];
        foreach ($entries as $entry) {
            $ids[] = $entry['department_id'];
        }
        return array_unique($ids);
    }

    /**
     * @param array $codes
     * @return array
     * @throws ErrorException
     */
    public function getDepartmentsByCodes(array $codes): array
    {
        $departments = [];
        foreach ($codes as $code) {
            $departments[] = $this->getDepartmentByCode($code);
        }
        return $departments;
    }

    /**
     * @param array $ids
     * @return array
     * @throws ErrorException
     */
    public function getDepartments(array $ids): array
    {
        $departments = [];
        foreach ($ids as $id) {
            $departments[] = $this->getDepartmentById($id);
        }
        return $departments;
    }

    /**
     * ГЛАВНАЯ ФУНКЦИЯ
     *
     * Строит дерево подразделений
     *
     * @param array $departments
     * @return array
     * @throws ErrorException
     */
    public function buildDepartmentTree(array $departments)
    {
        foreach ($departments as $key => $department) {
            // если для текущего подразделения найдено родительское подразделение
            // в массиве $departments, то добавляем к найденному родительскому подразделению
            // текущее подразделение в качестве дочернего
            if ($this->findParent($departments, $department)) {
                unset($departments[$key]);
                continue;
            }

            // перестановка
            if ($parentId = ((int) $department['parent_id'])) {
                $parentDepartment = $this->getDepartmentById($parentId);
                $parentDepartment['child_departments'][] = $department;
                $departments[$key] = $parentDepartment;
            }
        }
        return $departments;
    }

    /**
     * САМЫЙ СЛОЖНЫЙ АЛГОРИТМ!!!
     *
     * ОБХОД ПО ДЕРЕВУ ПОДРАЗДЕЛЕНИЙ
     *
     * @param array $departments
     * @param array $currentDepartment
     * @return bool
     */
    public function findParent(array &$departments, array $currentDepartment): bool
    {
        foreach ($departments as $key => $department) {
            if ($department['id'] === $currentDepartment['parent_id']) {
                $departments[$key]['child_departments'][] = $currentDepartment;
                return true;
            }
            if (!empty($department['child_departments'])) {
                return $this->findParent($department['child_departments'], $currentDepartment);
            }
        }
        return false;
    }

    /**
     * @param array $entries
     * @param array $departmentTree
     * @return array
     */
    public function bindEntriesWithDepartments(array $entries, array $departmentTree): array
    {
        foreach ($entries as $entry) {
            $this->bindEntry($entry, $departmentTree);
        }
        return $departmentTree;
    }

    /**
     * СВЯЗКА ПО КОДУ ПОДРАЗДЕЛЕНИЯ!!!
     *
     * @param array $entry
     * @param array $departments
     */
    public function bindEntry(array $entry, array &$departments)
    {
        foreach ($departments as $key => $department) {
            if ($department['code'] === $entry['department_code']) {
                $departments[$key]['entries'][] = $entry;
                break;
            }
            if (!empty($department['child_departments'])) {
                $this->bindEntry($entry, $departments[$key]['child_departments']);
            }
        }
    }

    /**
     * СВЯЗКА ПО ИДЕНТИФИКАТОРУ ПОДРАЗДЕЛЕНИЯ!!!
     *
     * @param array $entry
     * @param array $departments
     */
    public function bindEntry_old(array $entry, array &$departments)
    {
        foreach ($departments as $key => $department) {
            if ($department['id'] === $entry['department_id']) {
                $departments[$key]['entries'][] = $entry;
                break;
            }
            if (!empty($department['child_departments'])) {
                $this->bindEntry($entry, $departments[$key]['child_departments']);
            }
        }
    }

    /**
     * @param string $departmentId
     * @return array
     * @throws ErrorException
     */
    public function getDataByDepartmentId(string $departmentId)
    {
        return $this->collectData([$this->getDepartmentById($departmentId)]);
    }

    /**
     * РЕКУРСИВНАЯ ФУНКЦИЯ!
     *
     * @param array $departments
     * @return array
     * @throws ErrorException
     */
    public function collectData(array $departments)
    {
        foreach ($departments as $key => $department) {
            $departments[$key]['entries'] = $this->getEntriesByDepartmentCode($department['code']);
            if ($rows = $this->getChildDepartments($department['id'])) {
                $departments[$key]['child_departments'] = $this->collectData($rows);
            }
        }
        return $departments;
    }

    /**
     * При каждом запуске функция очищает таблицы 'department' и 'entry'.
     * Обновлять содержимое таблиц не вижу смысла.
     *
     * Обновляет дынные справочника данными из 1С
     */
    public function actualize(): void
    {
        $this->updateWith1cData();
        $this->updateWithIstuData();
    }

    /**
     * Получает данные от сервиса 1С
     * и сохраняет их в базе данных приложения
     */
    private function updateWith1cData(): void
    {
        try {
            $dataSections = [
                [
                    'data' => DataReceive1cService::getData('department'),
                    'modelClass'        => 'Novostruev\model\Department',
                    'dataClearingSql'   => 'TRUNCATE department'
                ],
                [
                    'data' => DataReceive1cService::getData('entry'),
                    'modelClass'        => 'Novostruev\model\Entry',
                    'dataClearingSql'   => 'TRUNCATE entry'
                ],
            ];
            $this->updateDataSections($dataSections);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     *
     */
    private function updateWithIstuData(): void
    {
        try {
            $istuDepartments = $this->getService('department')->getData();
            foreach ($istuDepartments as $istuDepartment) {
                if (empty($istuDepartment->code)) {
                    continue;
                }
                $department = $this->getDepartmentByCode((int) $istuDepartment->code);
                if (!empty($department)) {
                    $sql = "UPDATE department SET istu_id = ? WHERE id = ?";
                    self::$db->execute($sql, [(int) $istuDepartment->id, $department['id']]);
                }
            }

            $staff = $this->getService('staff')->getData();
            foreach ($staff as $employee) {
                if (empty($employee->code)) {
                    continue;
                }
                $entry = $this->getEntryByStaff1cCode((string) $employee->code);
                if (!empty($entry)) {
                    $sql = "UPDATE entry SET staff_istu_id = ? WHERE id = ?";
                    self::$db->execute($sql, [(int) $employee->id, $entry['id']]);
                }
            }
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param string $serviceName
     * @return Service
     * @throws ErrorException
     */
    public function getService(string $serviceName): Service
    {
        return ServiceManager::getService($serviceName);
    }

    /**
     * @param array $dataSections
     * @throws ErrorException
     */
    private function updateDataSections(array $dataSections)
    {
        foreach ($dataSections as $dataSection) {
            if ($dataSection['data']) {
                self::$db->exec($dataSection['dataClearingSql']); // Полная очистка таблицы
                foreach ($dataSection['data'] as $object) {
                    $this->updateDataSection(new $dataSection['modelClass']($object));
                }
            }
        }
    }

    /**
     * @param Model $model
     * @throws ErrorException
     */
    private function updateDataSection(Model $model)
    {
        self::$db->execute($model->getInsertSql(), $model->getInsertedValues());
    }

    /**
     * ИСПОЛЬЗУЕТСЯ ПРИ ОБНОВЛЕНИИ ДАННЫХ
     *
     * @param string $staffCode
     * @return array
     * @throws ErrorException
     */
    public function getEntryByStaff1cCode(string $staffCode): array
    {
        $sql = "SELECT id FROM entry WHERE staff_1c_code = ?";
        return self::$db->execute($sql, [$staffCode], true, true);
    }

    /**
     * ВАЖНАЯ ФУНКЦИЯ!
     *
     * @param int $departmentCode
     * @return array|int|mixed
     * @throws ErrorException
     */
    public function getEntriesByDepartmentCode(int $departmentCode)
    {
        $sql = "SELECT * FROM entry WHERE department_code = ? ORDER BY staff_is_head DESC";
        return self::$db->execute($sql, [$departmentCode], true);
    }

    /**
     * @param string $internalPhoneNumber
     * @return array|int|mixed
     * @throws ErrorException
     */
    public function getEntryByInternalPhoneNumber(string $internalPhoneNumber)
    {
        $sql = "SELECT * FROM entry WHERE internal_phone_number = ?";
        return self::$db->execute($sql, [$internalPhoneNumber], true, true);
    }

    /**
     * @param string $parentId
     * @return array|false
     * @throws ErrorException
     */
    protected function getChildDepartments(string $parentId)
    {
        $sql = "SELECT * FROM department WHERE parent_id = ?";
        return self::$db->execute($sql, [$parentId], true);
    }

    /**
     * @param string $id
     * @return array|false
     * @throws ErrorException
     */
    public function getDepartmentById(string $id)
    {
        $sql = "SELECT * FROM department WHERE id = ?";
        return self::$db->execute($sql, [$id], true, true);
    }

    /**
     * @param int $code
     * @return array|false
     * @throws ErrorException
     */
    public function getDepartmentByCode(int $code)
    {
        $sql = "SELECT * FROM department WHERE code = ?";
        return self::$db->execute($sql, [$code], true, true);
    }

    /**
     * Извлекает записи только тех подразделений,
     * для которых есть записи справочника в таблице entry
     *
     * @param string $orderBy
     * @return mixed
     * @throws ErrorException
     */
    public function getNotMainDepartments(string $orderBy=''): array
    {
        $parentId = '00000000-0000-0000-0000-000000000000';
        $sql = "SELECT * FROM department d WHERE d.parent_id != ? AND (SELECT COUNT(*) FROM entry e WHERE e.department_code = d.code GROUP BY e.department_code) > 0";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        return self::$db->execute($sql, [$parentId], true);
    }

    /**
     * @return mixed
     * @throws ErrorException
     */
    public function getMainDepartment()
    {
        $parentId = '00000000-0000-0000-0000-000000000000';
        $sql = "SELECT * FROM department WHERE parent_id = ?";
        return self::$db->execute($sql, [$parentId], true, true);
    }

    /**
     * @return array
     * @throws ErrorException
     */
    public function getMainDepartments(): array
    {
        $parentId = '00000000-0000-0000-0000-000000000000';
        $sql = "SELECT * FROM department WHERE parent_id = ?";
        return self::$db->execute($sql, [$parentId], true);
    }

    /**
     * @param string $search
     * @return array|int|mixed
     * @throws ErrorException
     */
    public function findEntries(string $search)
    {
        $str = "%$search%";
        $values = [$str, $str, $str, $str, $str, $str, $str];

        $sql = "SELECT * FROM entry WHERE title LIKE ? OR post LIKE ? OR staff_fullname LIKE ? "
            ."OR direct_phone_number LIKE ? OR internal_phone_number LIKE ? "
            ."OR location LIKE ? OR email LIKE ? ORDER BY department_code";

        return self::$db->execute($sql, $values, true);
    }
}






