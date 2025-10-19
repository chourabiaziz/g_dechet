<?php

namespace App\Form;

use App\Entity\Tracabilite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TracabiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('historique', TextareaType::class, [
                'label' => 'Historique de traçabilité',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Entrez l\'historique complet du déchet...'
                ]
            ])
            ->add('dateMiseAJour', DateTimeType::class, [
                'label' => 'Date de mise à jour',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control datetimepicker'
                ],
                'html5' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tracabilite::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}