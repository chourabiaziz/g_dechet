<?php

namespace App\Form;

use App\Entity\Dechet;
use App\Entity\Source;
use App\Entity\Infrastructure;
use App\Entity\Acteur;
use App\Entity\ProduitRecycle;
use App\Entity\Tracabilite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de déchet',
                'attr' => [
                    'class' => 'form-select',
                    'data-bs-toggle' => 'tooltip',
                    'title' => 'Sélectionnez le type de déchet'
                ],
                'choices' => [
                    'Plastique' => 'plastique',
                    'Métal' => 'metal',
                    'Verre' => 'verre',
                    'Papier/Carton' => 'papier_carton',
                    'Organique' => 'organique',
                    'Électronique' => 'electronique',
                    'Dangereux' => 'dangereux',
                    'Autre' => 'autre'
                ],
                'placeholder' => 'Choisir un type...'
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité (kg)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 0.1,
                    'placeholder' => '0.00'
                ],
                'html5' => true
            ])
            ->add('dateProduction', DateTimeType::class, [
                'label' => 'Date de production',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control datetimepicker'
                ],
                'html5' => false,
                'data' => new \DateTime() // Set the default value to the current date and time
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État initial',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Déclaré' => 'declare',
                    'En attente' => 'en_attente',
                    'En traitement' => 'en_traitement',
                    'Traité' => 'traite',
                    'Recyclé' => 'recycle'
                ]
            ])
            ->add('source', EntityType::class, [
                'label' => 'Source du déchet',
                'class' => Source::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez une source...',
                'required' => false
            ])
            ->add('infrastructure', EntityType::class, [
                'label' => 'Infrastructure',
                'class' => Infrastructure::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez une infrastructure...',
                'required' => false
            ])
            ->add('acteur', EntityType::class, [
                'label' => 'Acteur responsable',
                'class' => Acteur::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez un acteur...',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dechet::class,
            'attr' => ['class' => 'needs-validation', 'novalidate' => true]
        ]);
    }
}