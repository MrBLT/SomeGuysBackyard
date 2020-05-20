<?php
/**
 *
 * @Author: bthrower
 * @CreateAt: 8/29/2019 2:44 PM
 * Project: intranet-widgets-dev
 * File Name: PasswordResetType.php
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class)
            ->add('new_password1',PasswordType::class,
                [
                    'label' => 'Enter new password',
                    'constraints' => [
                        new Length(['min' => 8])
                    ]
                ])
            ->add('new_password2', PasswordType::class,
                [
                    'label' => 'Reenter new password',
                    'constraints' => [
                        new Length(['min' => 8])
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
