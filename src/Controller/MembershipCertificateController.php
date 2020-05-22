<?php
/**
 * @Author: bthrower
 * @CreateAt: 5/22/2020 9:46 AM
 * Project: SomeGuysBackyard
 * File Name: MembershipCertificateController.php
 */

namespace App\Controller;

use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MembershipCertificateController extends AbstractController
{
    /**
     * @Route("/certificate/view/{id}", name="app_membership_certificate")
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Member $member)
    {
        return $this->render('certificate/view.html.twig', [
            'user' => $member,
        ]);
    }
}
