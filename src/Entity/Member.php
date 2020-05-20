<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
final class Member implements UserInterface
{
    use BaseEntity;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     * @Assert\NotBlank
     */
    protected $email;

    /**
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your name cannot contain a number"
     * )
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    protected $firstName;

    /**
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your name cannot contain a number"
     * )
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     * @Assert\Regex(
     *     pattern="/[\(\)0-9 - ext.]+/",
     *     match=true,
     *     message="[\(\)0-9 -ext.]+"
     * )
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $address2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    protected $state;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $zipcode;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDonor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $uploadDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $joinDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PasswordResetRequest", mappedBy="member", orphanRemoval=true)
     */
    private $passwordResetRequests;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MemberNumber", mappedBy="member", cascade={"persist", "remove"})
     */
    private $memberNumber;

    public function __construct()
    {
        $this->setIsDonor(false);
        $this->passwordResetRequests = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->getEmail();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every member at least has ROLE_USER
        $roles[] = 'ROLE_MEMBER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     *
     * @return Member
     */
    public function setCountry($country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getIsDonor(): ?bool
    {
        return $this->isDonor;
    }

    public function setIsDonor(?bool $isDonor): self
    {
        $this->isDonor = $isDonor;

        return $this;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(?\DateTimeInterface $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getJoinDate(): ?\DateTimeInterface
    {
        return $this->joinDate;
    }

    public function setJoinDate(?\DateTimeInterface $joinDate): self
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    /**
     * @return Collection|PasswordResetRequest[]
     */
    public function getPasswordResetRequests(): Collection
    {
        return $this->passwordResetRequests;
    }

    public function addPasswordResetRequest(PasswordResetRequest $passwordResetRequest): self
    {
        if (!$this->passwordResetRequests->contains($passwordResetRequest)) {
            $this->passwordResetRequests[] = $passwordResetRequest;
            $passwordResetRequest->setMember($this);
        }

        return $this;
    }

    public function removePasswordResetRequest(PasswordResetRequest $passwordResetRequest): self
    {
        if ($this->passwordResetRequests->contains($passwordResetRequest)) {
            $this->passwordResetRequests->removeElement($passwordResetRequest);
            // set the owning side to null (unless already changed)
            if ($passwordResetRequest->getMember() === $this) {
                $passwordResetRequest->setMember(null);
            }
        }

        return $this;
    }

    public function getMemberNumber(): ?MemberNumber
    {
        return $this->memberNumber;
    }

    public function setMemberNumber(MemberNumber $memberNumber): self
    {
        $this->memberNumber = $memberNumber;

        // set the owning side of the relation if necessary
        if ($memberNumber->getMember() !== $this) {
            $memberNumber->setMember($this);
        }

        return $this;
    }
}
