<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 1/8/2019 10:06 AM
 * Project: EncounterTheCross
 * File Name: PersonWithAgreedTerms.php
 */

namespace App\Model\User;


use Doctrine\ORM\Mapping as ORM;

abstract class PersonWithAgreedTerms extends Person
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $agreedTermsAt;

    public function getAgreedTermsAt(): ?\DateTimeInterface
    {
        return $this->agreedTermsAt;
    }

    public function setAgreedTermsAt()
    {
        $this->agreedTermsAt = new \DateTime();
        return $this;
    }
}
