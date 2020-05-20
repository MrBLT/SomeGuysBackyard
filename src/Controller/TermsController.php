<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 5/20/2020 8:37 AM
 * Project: SomeGuysBackyard
 * File Name: TermsController.php
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TermsController extends AbstractController
{
    /**
     * @Route("/terms", name="app_terms")
     */
    public function show()
    {
        return $this->render('terms/terms_and_conditions.html.twig');
    }
}
