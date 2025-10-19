<?php

namespace App\Form;

use App\Entity\Infrastructure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfrastructureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'infrastructure',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Centre de tri municipal...'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'infrastructure',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Centre de tri' => 'centre_tri',
                    'Usine de recyclage' => 'usine_recyclage',
                    'Site de compostage' => 'site_compostage',
                    'Déchetterie' => 'dechetterie',
                    'Centre de collecte' => 'centre_collecte',
                    'Unité de traitement' => 'unite_traitement',
                    'Plateforme logistique' => 'plateforme_logistique'
                ],
                'placeholder' => 'Sélectionnez un type...'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Infrastructure::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}