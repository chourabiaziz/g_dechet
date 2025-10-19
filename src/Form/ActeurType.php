<?php

namespace App\Form;

use App\Entity\Acteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'acteur',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de l\'acteur...'
                ]
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Producteur' => 'producteur',
                    'Collecteur' => 'collecteur',
                    'Recycleur' => 'recycleur',
                    'Transformateur' => 'transformateur',
                    'Distributeur' => 'distributeur',
                    'Consommateur' => 'consommateur',
                    'Autorité' => 'autorite',
                    'Association' => 'association'
                ],
                'placeholder' => 'Sélectionnez un rôle...'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Acteur::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}