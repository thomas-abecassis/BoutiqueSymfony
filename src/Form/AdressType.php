<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    "label" => "Nom d'adresse",
                    "attr" => [
                        "placeholder" => "Entrez le nom de votre adresse (Mon domicile, Cave de super-héros...)"
                    ]
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    "label" => "Prénom",
                    "attr" => [
                        "placeholder" => "Entrez votre prénom"
                    ]
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    "label" => "Nom",
                    "attr" => [
                        "placeholder" => "Entrez votre nom"
                    ]
                ]
            )
            ->add(
                'company',
                TextType::class,
                [
                    "required" => false,
                    "label" => "Nom d'entreprise",
                    "attr" => [
                        "placeholder" => "Entrez le nom de votre enterprise (si achat via entreprise)"
                    ]
                ]
            )
            ->add(
                'adresse',
                TextType::class,
                [
                    "label" => "Adresse",
                    "attr" => [
                        "placeholder" => "Entrez votre adresse"
                    ]
                ]
            )
            ->add(
                'postal',
                TextType::class,
                [
                    "label" => "Code Postal",
                    "attr" => [
                        "placeholder" => "00000"
                    ]
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    "label" => "Ville",
                    "attr" => [
                        "placeholder" => "Entrez le nom de votre ville"
                    ]
                ]
            )
            ->add(
                'country',
                CountryType::class,
                [
                    "label" => "Pays",
                    "attr" => [
                        "placeholder" => "Entrez votre pays"
                    ]
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                    "label" => "Téléphone",
                    "attr" => [
                        "placeholder" => "000000000"
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ["label" => "ajouter mon adresse"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
