<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\model;

use stdClass;

class Department extends Model
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $parentId;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $humanName;

    /**
     * @var int
     */
    private $isUsed = 1;

    /**
     * Department constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->setId($data);
        $this->setParentId($data);
        $this->setCode($data);
        $this->setName($data);
        $this->setHumanName($data);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param stdClass $data
     */
    public function setId(stdClass $data): void
    {
        $this->id = $data->DepartmentId;
    }

    /**
     * @return string
     */
    public function getParentId(): string
    {
        return $this->parentId;
    }

    /**
     * @param stdClass $data
     */
    public function setParentId(stdClass $data): void
    {
        $this->parentId = $data->ParentDepartmentId;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param stdClass $data
     */
    public function setCode(stdClass $data): void
    {
        $this->code = (int) $data->DepartmentCode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param stdClass $data
     */
    public function setName(stdClass $data): void
    {
        $this->name = (string) $data->DepartmentName;
    }

    /**
     * @return string
     */
    public function getHumanName(): string
    {
        return $this->humanName;
    }

    /**
     * @param stdClass $data
     */
    public function setHumanName(stdClass $data): void
    {
        $this->humanName = (string) $data->DepartmentHumanName;
    }

    /**
     * @return int
     */
    public function getIsUsed(): int
    {
        return $this->isUsed;
    }

    /**
     * @param stdClass $data
     */
    public function setIsUsed(stdClass $data): void
    {
        $this->isUsed = 1;
    }

    /**
     * @return string
     */
    public function getInsertSql(): string
    {
        return "INSERT INTO department (id, parent_id, code, name, human_name) VALUES (?, ?, ?, ?, ?)";
    }

    /**
     * @return array
     */
    public function getInsertedValues(): array
    {
        return [
            $this->getId(),
            $this->getParentId(),
            $this->getCode(),
            $this->getName(),
            $this->getHumanName(),
        ];
    }
}
