<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('email', EmailType::class)
            // don't use password: avoid EVER setting that on a
            // field that might be persisted
            ->add('plainPassword1', PasswordType::class, [
                'label' => 'Password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choose a password!',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Come on, you can think of a password longer than that!',
                    ]),
                ],
            ])
            ->add('plainPassword2', PasswordType::class, [
                'label' => 'Repeat Password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Re-Enter The Password',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
