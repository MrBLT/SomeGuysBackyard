<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 10/8/2019 8:13 AM
 * Project: intranet-widgets-dev
 * File Name: BasePersonWithUUID.php
 */

namespace App\Model\User;


trait UUID
{
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
