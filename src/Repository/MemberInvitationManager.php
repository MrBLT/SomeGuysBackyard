<?php
/**
 * @Author: bthrower
 * @CreateAt: 8/30/2019 8:20 AM
 * Project: intranet-widgets-dev
 * File Name: MemberInvitationRepository.php
 */

namespace App\Repository;

use App\Entity\MemberInvitation;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class MemberInvitationRepository.
 */
class MemberInvitationManager extends BaseRepository
{
    /**
     * MemberInvitationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberInvitation::class);
    }

    /**
     * @param MemberInvitation $userInvitation
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addInvitation(MemberInvitation $userInvitation)
    {
        $em = $this->getEntityManager();
        $em->persist($userInvitation);
        $em->flush();
    }

    private function sendInvite(MemberInvitation $invitation)
    {
    }
}
