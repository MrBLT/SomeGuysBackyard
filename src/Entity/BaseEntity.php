<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 10/4/2019 10:23 AM
 * Project: intranet-widgets-dev
 * File Name: BaseEntity.php
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;

trait BaseEntity
{
    use TimestampableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @return \Ramsey\Uuid\UuidInterface | null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Ramsey\Uuid\UuidInterface $id
     */
    public function setId(\Ramsey\Uuid\UuidInterface $id): void
    {
        $this->id = $id;
    }
}
