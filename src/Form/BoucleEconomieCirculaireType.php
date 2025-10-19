<?php

namespace App\Form;

use App\Entity\BoucleEconomieCirculaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoucleEconomieCirculaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la boucle',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Recyclage plastique local...'
                ]
            ])
            ->add('objectif', TextareaType::class, [
                'label' => 'Objectif de la boucle',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez l\'objectif de cette boucle d\'économie circulaire...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BoucleEconomieCirculaire::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}