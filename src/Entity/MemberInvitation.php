<?php
/**
 * @Author: bthrower
 * @CreateAt: 8/30/2019 8:11 AM
 * Project: intranet-widgets-dev
 * File Name: MemberInvitation.php
 */

namespace App\Entity;

use App\Model\User\BasePerson;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberInvitationManager")
 */
class MemberInvitation extends BasePerson
{
    use BaseEntity;

    public const INVITATION_EXPIRATION_DURATION_DATE_INTERVAL = '30 days';

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="date")
     */
    private $createDate;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     * @Assert\Email
     * @Assert\NotBlank
     */
    protected $email;

    /**
     * @ORM\Column(type="date")
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $userCreated = false;

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return mixed
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }

    public function isFulfilled()
    {
        return $this->getUserCreated();
    }

    /**
     * @param mixed $userCreated
     */
    public function setUserCreated($userCreated): void
    {
        $this->userCreated = $userCreated;
    }

    public function __construct()
    {
        $this->createDate = new \DateTime();
        $this->expirationDate = date_add(new \DateTime(), date_interval_create_from_date_string(self::INVITATION_EXPIRATION_DURATION_DATE_INTERVAL));
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     *
     * @return MemberInvitation
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }
}
