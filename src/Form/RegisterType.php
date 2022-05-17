<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ["label" => "Email", 'attr' => ["placeholder" => "exemple@exemple.exemple"]])
            ->add('firstname', TextType::class, ["label" => "Prenom", 'attr' => ["placeholder" => "Jean"]])
            ->add('lastname', TextType::class, ["label" => "Nom", 'attr' => ["placeholder" => "Exemple"]])
            ->add('password', PasswordType::class, ["label" => "Mot de passe", 'attr' => ["placeholder" => ""]])
            ->add('password_confirm', PasswordType::class, ["label" => "Mot de passe", 'mapped' => false, 'attr' => ["placeholder" => "Confirmez votre mot de passe"]])
            ->add('submit', SubmitType::class, ["label" => "Submit"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
