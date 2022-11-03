<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev;

use ErrorException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Export
{
    /**
     * Каталог для хранения экспортируемых данных
     * Находится в корне приложения
     */
    const EXPORT_DIR = 'export';

    /**
     * Название генерируемого Excel-файла
     */
    const FILENAME = 'phonebook.xlsx';

    /**
     * Генерирует и сохраняет Excel-файл с записями справочника
     *
     * @throws ErrorException
     */
    public static function toExcel(): void
    {
        $data = App::$db->execute(self::getSql(), [], true);
        $preparedData = self::prepareData($data);
        try {
            self::saveSpreadsheet(self::createSpreadsheet($preparedData));
        } catch (PhpSpreadsheetException $e) {
            exit();
        }
    }

    /**
     * @return string
     */
    private static function getSql(): string
    {
        return "SELECT e.title, e.staff_fullname, e.internal_phone_number, "
            . "e.direct_phone_number, e.email, e.location, d.name department_name, "
            . "dp.name department_parent_name "
            . "FROM entry e "
            . "INNER JOIN department d ON e.department_code = d.code "
            . "INNER JOIN department dp ON d.parent_id = dp.id";
    }

    /**
     * @param array $rows
     * @return array
     */
    private static function prepareData(array $rows): array
    {
        $data = [];
        foreach ($rows as $row) {
            $line = [];
            $line[] = $row['title'];
            $line[] = $row['staff_fullname'];
            $line[] = $row['internal_phone_number'];
            $line[] = $row['direct_phone_number'];
            $line[] = $row['email'];
            $line[] = $row['location'];
            $line[] = $row['department_name'];
            $line[] = $row['department_parent_name'];

            $data[] = $line;
        }
        return $data;
    }

    /**
     * Создаёт электронную таблицу в оперативной памяти
     *
     * @param array $preparedData
     * @return Spreadsheet
     */
    private static function createSpreadsheet(array $preparedData): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Телефонный справочник ИжГТУ')
            ->setCellValue('A1', 'Название/Должность')
            ->setCellValue('B1', 'ФИО')
            ->setCellValue('C1', 'Внутренний номер')
            ->setCellValue('D1', 'Прямой городской номер')
            ->setCellValue('E1', 'Email')
            ->setCellValue('F1', 'Расположение')
            ->setCellValue('G1', 'Подразделение')
            ->setCellValue('H1', 'Родительское подразделение')
            ->fromArray($preparedData, null, 'A2');

        // Применяем автофильтр ко всему рабочему листу
        $spreadsheet->getActiveSheet()->setAutoFilter(
            $spreadsheet->getActiveSheet()
                ->calculateWorksheetDimension()
        );
        return $spreadsheet;
    }

    /**
     * Сохраняет таблицу в файл и возвращает его полный путь
     *
     * @param Spreadsheet $spreadsheet
     * @throws WriterException
     */
    private static function saveSpreadsheet(Spreadsheet $spreadsheet): void
    {
        (new Xlsx($spreadsheet))->save(self::getFullpath(self::FILENAME));
    }

    /**
     * Получает полный путь к файлу таблицы
     *
     * @param string $filename
     * @return string
     */
    private static function getFullpath(string $filename): string
    {
        if (!file_exists($exportDirPath = self::getPathToExportDir())) {
            mkdir($exportDirPath, 0755, true);
        }
        return $exportDirPath . DIRSEP . $filename;
    }

    /**
     * @return string
     */
    private static function getPathToExportDir(): string
    {
        return dirname(__DIR__) . DIRSEP . self::EXPORT_DIR;
    }
}
