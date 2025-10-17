<?php

namespace App\Form;

use App\Entity\User;
use Rinvex\Country\CountryLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your first name']),
                ],
            ])
            ->add('prename', TextType::class, [
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your last name']),
                ],
            ])
            ->add('countryCode', ChoiceType::class, [
                'label' => 'Code pays',
                'choices' => $this->getCountryCodesWithFlags(),
                'attr' => [
                    'class' => 'form-control country-code-select',
                ],
                'placeholder' => 'Sélectionnez un pays',
                'constraints' => [
                    new NotBlank(['message' => 'Le code pays est obligatoire']),
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['data-flag' => $this->getFlagFromCountryCode($value)];
                },
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'class' => 'form-control phone-number',
                    'placeholder' => 'Votre numéro de téléphone'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire']),
                    new Regex([
                        'pattern' => '/^[0-9+\s]+$/',
                        'message' => 'Le numéro de téléphone n’est pas valide'
                    ]),
                ],
            ])
 
           
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }private function getCountryCodesWithFlags(): array
    {
        $countries = CountryLoader::countries(true, true);
        $choices = [];

        foreach ($countries as $country) {
            $callingCodes = $country->getCallingCodes();
            if (!empty($callingCodes)) {
                $code = $callingCodes[0];
                $emoji = $this->convertIsoToFlag($country->getIsoAlpha2());
                $choices[$emoji . ' ' . $country->getName() . ' (+' . $code . ')'] = '+' . $code;
            }
        }

        return $choices;
    }

    private function getFlagFromCountryCode(string $dialCode): string
    {
        $countries = CountryLoader::countries(true, true);
        foreach ($countries as $country) {
            $codes = $country->getCallingCodes();
            if (!empty($codes) && in_array(ltrim($dialCode, '+'), $codes)) {
                return $this->convertIsoToFlag($country->getIsoAlpha2());
            }
        }
        return '🏳️';
    }

    private function convertIsoToFlag(string $iso): string
    {
        $iso = strtoupper($iso);
        return mb_convert_encoding(
            '&#' . (127397 + ord($iso[0])) . ';&#' . (127397 + ord($iso[1])) . ';',
            'UTF-8',
            'HTML-ENTITIES'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }}
