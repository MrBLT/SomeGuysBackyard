<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\MemberInvitation;
use App\Entity\PasswordResetRequest;
use App\Form\InviteUserFormType;
use App\Form\PasswordForgotResetType;
use App\Form\PasswordForgotType;
use App\Form\UserRegistrationFormType;
use App\Repository\MemberInvitationManager;
use App\Repository\MemberRepository;
use App\Security\LoginFormAuthenticator;
use App\Security\RolesRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/forgot", name="app_forgot")
     *
     * @param Request $request
     * @param MemberRepository $userRepository
     *
     * @param MailerInterface $mailer
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function forgot(Request $request, MemberRepository $userRepository, MailerInterface $mailer)
    {
        $form = $this->createForm(PasswordForgotType::class);
        $form->handleRequest($request);
        $error = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            /** @var Member|null $user */
            $user = null;

            //Find the user that submitted the form
            try {
                $user = $userRepository->loadUserByUsername($email);
            } catch (NonUniqueResultException $e) {
                $error = ['message_data' => 'Duplicate Accounts detected. Please Contact someguysbackyard@gmail.com'];

                return $this->render(
                    'security/forgot.html.twig',
                    [
                        'form' => $form->createView(),
                        'error' => $error,
                    ]
                );
            }

            //If user is null it was not found. send error message
            if (null === $user) {
                $error = ['message_data' => 'Email does not exist in our database! Contact someguysbackyard@gmail.com for assistance if needed.'];

                return $this->render(
                    'security/forgot.html.twig',
                    [
                        'form' => $form->createView(),
                        'error' => $error,
                    ]
                );
            }

            //Create a PasswordResetRequest for the user
            $passwordResetRequest = new PasswordResetRequest();
            $passwordResetRequest->setMember($user);

            //Persist the PasswordResetRequest to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($passwordResetRequest);
            $em->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('SomeGuysBackyard@gmail.com', 'Some Guy\'s Backyard'))
                ->to(new Address($user->getEmail(), $user->getName()))
                ->priority(Email::PRIORITY_NORMAL)
                ->subject('Reset your SGB Account Password')
                ->htmlTemplate('emails/Registration/ForgotPassword/forgot_password.html.twig')
                ->context([
                    'passwordResetRequest' => $passwordResetRequest,
                    'user' => $user,
                ]);

            $mailer->send($email);

            //Render the notification saying the email was sent.
            return $this->render('security/forgot_sent.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->render(
            'security/forgot.html.twig',
            [
                'form' => $form->createView(),
                'error' => $error,
            ]
        );
    }

    /**
     * @Route("/reset/{id}", name="app_reset")
     *
     * @param PasswordResetRequest $passwordResetRequest
     * @param MemberRepository $userRepository
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $formAuthenticator
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function reset(PasswordResetRequest $passwordResetRequest,
                          MemberRepository $userRepository,
                          Request $request,
                          UserPasswordEncoderInterface $passwordEncoder,
                          GuardAuthenticatorHandler $guardHandler,
                          LoginFormAuthenticator $formAuthenticator,
                          AuthenticationUtils $authenticationUtils
    )
    {
        //Handle expired password reset requests
        if ($passwordResetRequest->isExpired()) {
            $lastUsername = $authenticationUtils->getLastUsername();
            $error = [
                'messageKey' => 'The password reset request has expired. Please click Forgot Password again to re-try.',
                'messageData' => ['en' => 'The password reset request has expired. Please click Forgot Password again to re-try.'],];

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        //Handle Password Reset Requests that have already been fulfilled
        if ($passwordResetRequest->isFulfilled()) {
            $lastUsername = $authenticationUtils->getLastUsername();
            $error = [
                'messageKey' => 'The password reset request was already fulfilled. Please click Forgot Password again to re-try, or contact helpdesk@vortexglobal.com for assistance.',
                'messageData' => ['en' => 'The password reset request has expired. Please click Forgot Password again to re-try.'],];

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        //Retrieve the user that needs password reset
        /** @var Member|null $user */
        $user = $passwordResetRequest->getMember();
        // dd($user);
        $form = $this->createForm(PasswordForgotResetType::class, $user);
        $form->handleRequest($request);
        $error = null;
        if ($form->isSubmitted()) {
            $email = $form->get('email');

            if ($user->getEmail() !== $email->getData()) {
                $emailError = new FormError('Invalid Email!');
                $email->addError($emailError);
            }

            $new1 = $form->get('plainPassword1')->getData();
            $new2 = $form->get('plainPassword2')->getData();

            //Check that the passwords entered are equivalent
            if ($new1 !== $new2) {
                $error = new FormError('Your new passwords don\'t match!');
                $form->get('plainPassword1')->addError($error);
                $form->get('plainPassword2')->addError($error);
            }

            if ($form->isValid()) {
                //Update user password
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $new1
                ));
                //Persist user to DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);

                //Fulfill the password reset request
                $passwordResetRequest->setFulfilled(true);
                //Persist the password reset request
                $em->persist($passwordResetRequest);

                //Flush objects to the database
                $em->flush();

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $formAuthenticator,
                    'main'
                );
            }
        }

        return $this->render('security/reset.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    /**
     * @Route("/invite", name="security_invite")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param Request $request
     * @param MemberInvitationManager $repository
     *
     * @param MailerInterface $mailer
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function invite(Request $request, MemberInvitationManager $repository, MailerInterface $mailer)
    {
        $form = $this->createForm(InviteUserFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Member $user */
            $user = $this->getUser();

            // dd($form->getData());
            $data = $form->getData();
            //Create MemberInvitation
            $invitation = new MemberInvitation();
            $invitation->setFirstName($data['firstName']);
            $invitation->setLastName($data['lastName']);
            $invitation->setEmail($data['email']);

            //set roles from form selection
            $rolesRepo = new RolesRepository();
            $roles = [];
            foreach ($data as $key => $datum) {
                if ($rolesRepo->isRole($key) && true === $datum) {
                    $roles[] = $key;
                }
            }
            $invitation->setRoles($roles);
            $repository->addInvitation($invitation);


            $email = (new TemplatedEmail())
                ->from(new Address('SomeGuysBackyard@gmail.com', 'Some Guy\'s Backyard'))
                ->to(new Address($user->getEmail(), $user->getName()))
                ->priority(Email::PRIORITY_NORMAL)
                ->subject('You have been invited to Some Guy\'s Backyard')
                ->htmlTemplate('emails/Registration/Invitation/invitation.html.twig')
                ->context([
                    'invitation' => $invitation,
                    'user' => $user,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('easyadmin', ['entity' => 'Member']);
            // return $this->render('emails/NewAdmin/new_admin.html.twig',[
            //     'invitation'=>$invitation,
            //     'user' => $user
            // ]);
        }

        return $this->render('security/invite.html.twig', [
            'inviteForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/{id}", name="app_register_invitation")
     *
     * @param MemberInvitation $invitation
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $formAuthenticator
     *
     * @return Response|null
     */
    public function registerInvitation(MemberInvitation $invitation, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {
        //Handle invitations that have already been fulfilled
        if ($invitation->isFulfilled()) {
            $lastUsername = '';
            $error = [
                'messageKey' => 'The invitation has already been fulfilled. Please contact your system administrator for assistance.',
                'messageData' => ['english' => 'The invitation has already been fulfilled. Please contact your system administrator for assistance.'],];

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        //Setup User from Invitation
        $member = new Member();
        $member->setEmail($invitation->getEmail());
        $member->setFirstName($invitation->getFirstName());
        $member->setLastName($invitation->getLastName());
        $member->setRoles($invitation->getRoles());

        $registerForm = $this->createForm(UserRegistrationFormType::class, $member);

        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted()) {
            // dd($registerForm);
            /** @var Member $user */
            $user = $registerForm->getData();

            $pw1 = $registerForm->get('plainPassword1')->getData();
            $pw2 = $registerForm->get('plainPassword2')->getData();
            if (!($pw1 === $pw2)) {
                $error = new FormError('Your new passwords don\'t match!');
                $registerForm->get('plainPassword1')->addError($error);
                $registerForm->get('plainPassword2')->addError($error);
            }

            if ($registerForm->isValid()) {
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $pw1
                ));

                $em = $this->getDoctrine()->getManager();

                //Persist user to DB
                $em->persist($user);
                $em->flush();

                //Update invitation to show that user was created
                $invitation->setUserCreated(true);
                $em->persist($invitation);
                $em->flush();

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $formAuthenticator,
                    'main'
                );
            }
            // $user->setEmail($request->request->get('email'));
            // $user->setFirstName('Mystery');
        }

        return $this->render('security/register.html.twig', [
            'registerForm' => $registerForm->createView(),
        ]);
    }
}
