<?php

namespace App\Controller;

use App\Form\NewPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }


    /**
     * @Route("/compte/mdp", name="app_account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;
        $user = $this->getUser();
        $form = $this->createForm(NewPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $notification = "Votre mot de passe actuel n'est pas le bon";
            $old_password = $form->get("old_password")->getData();

            if ($hasher->isPasswordValid($user, $old_password)) {
                $newPassword = $form->get("new_password")->getData();
                $hashedNewPassword = $hasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedNewPassword);

                $this->entityManager->flush();
                $notification = "Votre mot de passe a été changé";
            }
        }

        return $this->render('account/password.html.twig', [
            'controller_name' => 'AccountPasswordController',
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
