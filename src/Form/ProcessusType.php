<?php

namespace App\Form;

use App\Entity\Processus;
use App\Entity\Dechet;
use App\Entity\Autorite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du processus',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Tri manuel, Broyage...'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de processus',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Collecte' => 'collecte',
                    'Tri' => 'tri',
                    'Broyage' => 'broyage',
                    'Nettoyage' => 'nettoyage',
                    'Transformation' => 'transformation',
                    'Recyclage' => 'recyclage',
                    'Valorisation' => 'valorisation',
                    'Élimination' => 'elimination'
                ],
                'placeholder' => 'Sélectionnez un type...'
            ])
            ->add('autorite', EntityType::class, [
                'label' => 'Autorité responsable',
                'class' => Autorite::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez une autorité...',
                'required' => false
            ])
            ->add('dechets', EntityType::class, [
                'label' => 'Déchets associés',
                'class' => Dechet::class,
                'choice_label' => function(Dechet $dechet) {
                    return sprintf('#%d - %s (%s kg)', $dechet->getId(), $dechet->getType(), $dechet->getQuantite());
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'select'
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Processus::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}