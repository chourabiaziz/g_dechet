<?php

namespace App\Form;

use App\Entity\Source;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la source',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Ménages, Industrie textile...'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de source',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Ménages' => 'menages',
                    'Industrie' => 'industrie',
                    'Commerce' => 'commerce',
                    'Agriculture' => 'agriculture',
                    'Construction' => 'construction',
                    'Administration' => 'administration',
                    'Événementiel' => 'evenementiel'
                ],
                'placeholder' => 'Sélectionnez un type...'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Source::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}