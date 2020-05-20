<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 1/8/2019 9:28 AM
 * Project: EncounterTheCross
 * File Name: PersonWithPayment.php
 */

namespace App\Model\User;

use Doctrine\ORM\Mapping as ORM;

abstract class PersonWithPayment extends PersonWithAgreedTerms
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $paid = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $paymentMethod;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $chargeId;

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     */
    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     * @return PersonWithPayment
     */
    public function setPaymentMethod($paymentMethod): self 
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }


    public function setChargeId($chargeId): self 
    {
        $this->chargeId = $chargeId;
        return $this;
    }
    
    
}
