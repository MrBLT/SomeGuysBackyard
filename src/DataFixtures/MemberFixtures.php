<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\MemberNumber;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MemberFixtures extends BaseFixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $dev = new Member();
        $dev
            ->setEmail('bricethrower@gmail.com')
            ->setFirstName('Brice')
            ->setLastName('Thrower')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordEncoder->encodePassword($dev, 'admin'))
            ;

        $manager->persist($dev);

        $this->createMany(50, 'normal_members', function ($i) {
            $member = new Member();

            $member->setMemberNumber((new MemberNumber()));
            $member
                ->setEmail($this->faker->email)
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setIsDonor($this->faker->boolean)
                ->setJoinDate($this->faker->dateTimeThisMonth)
                ->setUploadDate($this->faker->dateTimeThisMonth)
                ->setPassword($this->passwordEncoder->encodePassword(
                $member,
                'etc'
            ));

            if (rand(1, 10) > 4) {
                $member->setPhone($this->faker->phoneNumber);
            }

            $member = $this->setMemberAddress($member);

            return $member;
        });

        $this->createMany(5, 'admin_members', function ($i) {
            $member = new Member();
            $member->setEmail(sprintf('admin%d@golf.com', $i));
            $member->setFirstName($this->faker->firstName);
            $member->setLastName($this->faker->lastName);
            $member->setJoinDate($this->faker->dateTimeThisMonth);
            $member->setUploadDate($this->faker->dateTimeThisMonth);
            $member->setPhone($this->faker->phoneNumber);
            $member->setRoles(['ROLE_ADMIN']);

            $member->setPassword($this->passwordEncoder->encodePassword(
                $member,
                'etc'
            ));

            $member = $this->setMemberAddress($member);

            return $member;
        });

        $manager->flush();
    }

    private function setMemberAddress(Member $member)
    {
        if (rand(1, 10) > 4) {
            $member->setAddress($this->faker->streetAddress)
                ->setCity($this->faker->city)
                ->setState($this->faker->state)
                ->setZipcode($this->faker->postcode)
                ->setCountry($this->faker->country);

            if (rand(1, 10) > 6) {
                $member->setAddress2($this->faker->secondaryAddress);
            }
        }

        return $member;
    }

    public function getOrder()
    {
        return 1;
    }
}
