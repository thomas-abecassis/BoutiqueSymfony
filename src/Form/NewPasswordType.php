<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                "label" => "Votre ancien mot de passe",
                "required" => true,
                "mapped" => false,
                'attr' => ["placeholder" => "Entrez votre ancien mot de passe"]
            ])
            ->add('new_password', RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Les mots de passe ne correspondent pas",
                "label" => "Votre nouveau mot de passe",
                "required" => true,
                "mapped" => false,
                "first_options" => ["label" => "Votre nouveau mot de passe", 'attr' => ["placeholder" => "Entrez votre mot de passe"]],
                "second_options" => ["label" => "Confirmez nouveau votre mot de passe", 'attr' => ["placeholder" => "Confirmez votre mot de passe"]]
            ])
            ->add('submit', SubmitType::class, ["label" => "Submit"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
