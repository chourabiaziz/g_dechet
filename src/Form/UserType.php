<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email address',
                'attr' => ['placeholder' => 'Enter your email'],
            ])
            ->add('roles')
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => ['placeholder' => 'Enter your password'],
            ])
            ->add('name', TextType::class, [
                'label' => 'Last name',
                'attr' => ['placeholder' => 'Enter your last name'],
            ])
            ->add('prename', TextType::class, [
                'label' => 'First name',
                'attr' => ['placeholder' => 'Enter your first name'],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone number',
                'attr' => ['placeholder' => '+216 ...'],
            ])
            ->add('registrationDate', DateType::class, [
                'label' => 'Registration date',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
