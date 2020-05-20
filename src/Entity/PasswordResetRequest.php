<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PasswordResetRequestRepository")
 */
class PasswordResetRequest
{
    public const PASSWORD_RESET_REQUEST_EXPIRATION_DURATION = '24 hours';

    use BaseEntity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="passwordResetRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\Column(type="date")
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="date")
     */
    private $createDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fulfilled;

    public function __construct()
    {
        $this->fulfilled = false;
        $this->createDate = new \DateTime();
        $this->expirationDate = date_add(new \DateTime(), date_interval_create_from_date_string(self::PASSWORD_RESET_REQUEST_EXPIRATION_DURATION));
    }

    /**
     * @return bool
     */
    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * @param bool $fulfilled
     */
    public function setFulfilled(bool $fulfilled): void
    {
        $this->fulfilled = $fulfilled;
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function isExpired()
    {
        $currentDate = new \DateTime();
        if ($currentDate > $this->expirationDate) {
            return true;
        }

        return false;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate(): \DateTime
    {
        return $this->createDate;
    }

    /**
     * @param \DateTime $createDate
     */
    public function setCreateDate(\DateTime $createDate): void
    {
        $this->createDate = $createDate;
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }
}
