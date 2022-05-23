<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options["user"];
        $builder
            ->add('delivery', EntityType::class, [
                "label" => false,
                "required" => true,
                "class" => Adress::class,
                "multiple" => false,
                "expanded" => true,
                "choices" => $user->getAdresses()
            ])
            ->add('carriers', EntityType::class, [
                "label" => "Choisir mon adresse de transporteur",
                "required" => true,
                "class" => Carrier::class,
                "multiple" => false,
                "expanded" => true,
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Valider mon panier",
                "attr" => [
                    "class" => "btn btn-primary btn-block"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "user" => array()
        ]);
    }
}
