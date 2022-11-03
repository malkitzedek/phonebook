<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\model;

use stdClass;

class Entry extends Model
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string
     */
    private $post;

    /**
     * @var string|null
     */
    private $staffFullname;

    /**
     * @var string|null
     */
    private $staff1cCode;

    /**
     * @var int
     */
    private $staffIsHead;

    /**
     * @var int|null
     */
    private $departmentCode;

    /**
     * @var string|null
     */
    private $directPhoneNumber;

    /**
     * @var string|null
     */
    private $internalPhoneNumber;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var int
     */
    private $isHidden;

    /**
     * Department constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->setTitle($data);
        $this->setPost($data);
        $this->setStaffFullname($data);
        $this->setStaff1cCode($data);
        $this->setStaffIsHead($data);
        $this->setDepartmentCode($data);
        $this->setDirectPhoneNumber($data);
        $this->setInternalPhoneNumber($data);
        $this->setLocation($data);
        $this->setEmail($data);
        $this->setIsHidden($data);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param stdClass $data
     */
    public function setTitle(stdClass $data): void
    {
        $this->title = $data->Title;
    }

    /**
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
    }

    /**
     * @param stdClass $data
     */
    public function setPost(stdClass $data): void
    {
        $this->post = $data->PostName;
    }

    /**
     * @return string|null
     */
    public function getStaffFullname(): ?string
    {
        return $this->staffFullname;
    }

    /**
     * @param stdClass $data
     */
    public function setStaffFullname(stdClass $data): void
    {
        $this->staffFullname = $data->StaffFio;
    }

    /**
     * @return string|null
     */
    public function getStaff1cCode(): ?string
    {
        return $this->staff1cCode;
    }

    /**
     * @param stdClass $data
     */
    public function setStaff1cCode(stdClass $data): void
    {
        $this->staff1cCode = $data->StaffCode;
    }

    /**
     * @return int
     */
    public function getStaffIsHead(): int
    {
        return $this->staffIsHead;
    }

    /**
     * @param stdClass $data
     */
    public function setStaffIsHead(stdClass $data): void
    {
        $this->staffIsHead = (int) $data->Staffhead;
    }

    /**
     * @return int|null
     */
    public function getDepartmentCode(): ?int
    {
        return $this->departmentCode;
    }

    /**
     * @param stdClass $data
     */
    public function setDepartmentCode(stdClass $data): void
    {
        $this->departmentCode = (int) $data->DepartmentCode;
    }

    /**
     * @return string|null
     */
    public function getDirectPhoneNumber(): ?string
    {
        return $this->directPhoneNumber;
    }

    /**
     * @param stdClass $data
     */
    public function setDirectPhoneNumber(stdClass $data): void
    {
        $this->directPhoneNumber = $data->PhoneNumber;
    }

    /**
     * @return string|null
     */
    public function getInternalPhoneNumber(): ?string
    {
        return $this->internalPhoneNumber;
    }

    /**
     * @param stdClass $data
     */
    public function setInternalPhoneNumber(stdClass $data): void
    {
        $this->internalPhoneNumber = $data->InternalPhoneNumber;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param stdClass $data
     */
    public function setLocation(stdClass $data): void
    {
        $this->location = $data->location;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param stdClass $data
     */
    public function setEmail(stdClass $data): void
    {
        $this->email = $data->Email;
    }

    /**
     * @return int
     */
    public function getIsHidden(): int
    {
        return $this->isHidden;
    }

    /**
     * @param stdClass $data
     */
    public function setIsHidden(stdClass $data): void
    {
        $this->isHidden = ((int) $data->isHidden) ? 1 : 0;
    }

    /**
     * @return string
     */
    public function getInsertSql(): string
    {
        return "INSERT INTO entry (title, post, staff_fullname, staff_1c_code, staff_is_head, department_code, direct_phone_number, internal_phone_number, location, email, is_hidden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }

    /**
     * @return array
     */
    public function getInsertedValues(): array
    {
        return [
            $this->getTitle(),
            $this->getPost(),
            $this->getStaffFullname(),
            $this->getStaff1cCode(),
            $this->getStaffIsHead(),
            $this->getDepartmentCode(),
            $this->getDirectPhoneNumber(),
            $this->getInternalPhoneNumber(),
            $this->getLocation(),
            $this->getEmail(),
            $this->getIsHidden(),
        ];
    }
}
