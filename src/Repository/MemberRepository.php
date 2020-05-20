<?php

namespace App\Repository;

use App\Entity\Member;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends BaseRepository implements UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    /**
     * @param string $email
     *
     * @return Member | null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByEmail(string $email)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException              if the user is not found
     * @throws \Doctrine\ORM\NonUniqueResultException if the username is duplicate
     */
    public function loadUserByUsername($username)
    {
        return $this->findOneByEmail($username);
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        if (Member::class === $class) {
            return true;
        }

        return false;
    }

    public function updateUserPassword(Member $user, string $hash)
    {
        $user->setPassword($hash);
    }
}
