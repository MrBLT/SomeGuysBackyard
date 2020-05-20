<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\MemberNumber;
use App\Form\RegistrationFormType;
use App\Form\SubscriptionFormType;
use App\Form\UnsubscribeConfirmFormType;
use App\Form\UnsubscribeFormType;
use App\Repository\MemberRepository;
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
                ]);

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

    /**
     * @Route("/unsubscribe", name="app_unsubscribe")
     */
    public function unsubscribe(Request $request, MemberRepository $memberRepository, MailerInterface $mailer)
    {
        $form = $this->createForm(UnsubscribeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $member = $memberRepository->findOneBy([
                'email' => $data['email'],
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
            ]);

            if ($member === null) {
                $error = new FormError('Unable to find requested account.');
                $form->addError($error);
            }

            if ($form->isValid()) {

                //Send an email to the person who wants to unsubscribe. This way only the owner of the email can unsubscribe
                $email = (new TemplatedEmail())
                    ->from(new Address('SomeGuysBackyard@gmail.com', 'Some Guy\'s Backyard'))
                    ->to(new Address($member->getEmail(), $member->getName()))
                    ->priority(Email::PRIORITY_NORMAL)
                    ->subject('Unsubscribe from Some Guy\'s Backyard')
                    ->htmlTemplate('emails/Registration/Unsubscribe/unsubscribe.html.twig')
                    ->context([
                        'id' => $member->getId(),
                    ]);

                $mailer->send($email);

                return $this->render('registration/unsubscribe_email_sent.html.twig');
            }
        }

        return $this->render('registration/unsubscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/unsubscribe/confirm/{id}", name="app_unsubscribe_confirm")
     */
    public function unsubscribeConfirm(Member $member, Request $request)
    {
        $form = $this->createForm(UnsubscribeConfirmFormType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null;
            if ($member === null) {
                $error = 'No account found.';
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($member);
                $entityManager->flush();
            }

            return $this->render('registration/unsubscribe_successful.html.twig', [
                'error' => $error,
            ]);
        }

        return $this->render('registration/unsubscribe_confirm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/unsubscribe/{id}", name="app_unsubscribe_remove")
     */
    public
    function removeAccount(Member $member)
    {
        $error = null;

        if ($member === null) {
            $error = 'No account found.';
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($member);
            $entityManager->flush();
        }

        return $this->render('registration/unsubscribe_successful.html.twig', [
            'error' => $error
        ]);
    }
}
