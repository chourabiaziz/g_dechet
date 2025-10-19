<?php

namespace App\Form;

use App\Entity\ProduitRecycle;
use App\Entity\BoucleEconomieCirculaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitRecycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit recyclé',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Granulés plastiques, Compost...'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de produit',
                'attr' => ['class' => 'form-select'],
                'choices' => [
                    'Matière première' => 'matiere_premiere',
                    'Produit fini' => 'produit_fini',
                    'Sous-produit' => 'sous_produit',
                    'Énergie' => 'energie',
                    'Compost' => 'compost',
                    'Granulés' => 'granules',
                    'Paillettes' => 'paillettes'
                ],
                'placeholder' => 'Sélectionnez un type...'
            ])
            ->add('boucle', EntityType::class, [
                'label' => 'Boucle d\'économie circulaire',
                'class' => BoucleEconomieCirculaire::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez une boucle...',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitRecycle::class,
            'attr' => ['class' => 'needs-validation']
        ]);
    }
}