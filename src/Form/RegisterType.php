<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ["label" => "Email", 'attr' => ["placeholder" => "exemple@exemple.exemple"]])
            ->add('firstname', TextType::class, [
                "label" => "Prenom",
                'constraints' => [new Length(['min' => 3, "max" => 20])],
                'attr' => ["placeholder" => "Jean"]
            ])
            ->add('lastname', TextType::class, ["label" => "Nom", 'attr' => ["placeholder" => "Exemple"]])
            ->add('password', RepeatedType::class, [
                "type" => PasswordType::class,
                "invalid_message" => "Les mots de passe ne correspondent pas",
                "label" => "Votre mot de passe",
                "required" => true,
                "first_options" => ["label" => "Votre mot de passe", 'attr' => ["placeholder" => "Entrez votre mot de passe"]],
                "second_options" => ["label" => "Confirmez votre mot de passe", 'attr' => ["placeholder" => "Confirmez votre mot de passe"]]
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
