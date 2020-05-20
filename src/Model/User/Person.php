<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 1/2/2019 3:08 PM
 * Project: EncounterTheCross
 * File Name: Person.php
 */

namespace App\Model\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Class Person
 *
 * This class contains extended details about a person.
 *
 * @package App\Model\User
 */
abstract class Person extends BasePerson
{

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
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     * @return Person
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

}
