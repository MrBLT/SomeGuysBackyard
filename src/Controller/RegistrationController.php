<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\MemberNumber;
use App\Form\RegistrationFormType;
use App\Form\SubscriptionFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/subscribe", name="app_subscribe")
     */
    public function subscribe(Request $request, MailerInterface $mailer)
    {
        $user = new Member();
        $form = $this->createForm(SubscriptionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setMemberNumber(new MemberNumber());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('SomeGuysBackyard@gmail.com', 'Some Guy\'s Backyard'))
                ->to(new Address($user->getEmail(), $user->getName()))
                ->priority(Email::PRIORITY_NORMAL)
                ->subject('Welcome to Some Guy\'s Backyard!')
                ->htmlTemplate('emails/Registration/NewSubscriber/welcome.html.twig')
                ->context([
                    'user' => $user,
                ])
            ;

            $mailer->send($email);

            return $this->render('registration/subscribe_successful.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->render('registration/subscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new Member();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pw1 = $form->get('plainPassword1')->getData();
            $pw2 = $form->get('plainPassword2')->getData();
            if (!($pw1 === $pw2)) {
                $error = new FormError('Passwords do not match!');
                $form->get('plainPassword1')->addError($error);
                $form->get('plainPassword2')->addError($error);
            }

            if ($form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword1')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/{id}", name="app_register_subscriber")
     */
    public function registerSubscriber(Member $member, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = $member;
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pw1 = $form->get('plainPassword1')->getData();
            $pw2 = $form->get('plainPassword2')->getData();
            if (!($pw1 === $pw2)) {
                $error = new FormError('Passwords do not match!');
                $form->get('plainPassword1')->addError($error);
                $form->get('plainPassword2')->addError($error);
            }

            if ($form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword1')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
