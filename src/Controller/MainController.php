<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/profile", name="app_profile")
     */
    public function profile(Security $security)
    {
        $member = $security->getUser();
        return $this->render('main/profile.html.twig', [
            'member' => $member,
        ]);
    }
}
