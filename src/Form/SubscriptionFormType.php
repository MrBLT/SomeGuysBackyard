<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'oninvalid' => "this.setCustomValidity('We need your First Name Please.')",
                    'oninput' => "setCustomValidity('')",
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'oninvalid' => "this.setCustomValidity('We need your Last Name Please.')",
                    'oninput' => "setCustomValidity('')",
                ]
            ])
            ->add('email', EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
