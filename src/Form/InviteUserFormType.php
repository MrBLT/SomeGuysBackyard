<?php

namespace App\Form;

use App\Security\RolesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteUserFormType extends AbstractType
{
    /**
     * @var array
     */
    private $roles;

    public function __construct()
    {
        $this->roles = (new RolesRepository())->getRoles();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email', EmailType::class);
        $choices = [];
        foreach ($this->roles as $role) {
            $builder->add($role['role'], CheckboxType::class, [
                'label' => $role['name'].' - '.$role['description'],
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
