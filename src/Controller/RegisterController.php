<?php

namespace App\Controller;

use Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userMail = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            //Si l'email n'est pas déjà renseigné pour un compte
            if (!$userMail) {
                $user->encodePassword($hasher);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = "Inscription complète";

                $mail = new Mail();
                $mail->send($user->getEmail(), "Inscription complète", "Bienvenue");
            } else {
                $notification = "Adresse e-mail déjà renseignée pour un client";
            }
        }

        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView(),
            "notification" => $notification
        ]);
    }
}
