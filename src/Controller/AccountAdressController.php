<?php

namespace App\Controller;

use Exception;
use App\Classe\Cart;
use App\Entity\Adress;
use App\Form\AdressType;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAdressController extends AbstractController
{

    private $doctrine;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->doctrine = $manager;
    }

    /**
     * @Route("/account/adress", name="app_account_adress")
     */
    public function index(): Response
    {
        return $this->render('account/adress.html.twig', [
            'controller_name' => 'AccountAdressController',
        ]);
    }

    /**
     * @Route("/account/adress/add/{redirection?}", name="app_account_adress_add")
     */
    public function addAdress(Request $reqest, $redirection = null): Response
    {

        $adress = new Adress();
        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($reqest);

        if ($form->isSubmitted() && $form->isValid()) {
            $adress->setClient($this->getUser());
            $this->doctrine->persist($adress);
            $this->doctrine->flush();

            // check if addAdress is called via an order summary
            if ($redirection == "panier")
                return $this->redirectToRoute("app_order");
            // check if addAdress is called via an order placement
            else if ($redirection == "commande")
                return $this->redirectToRoute("app_order");
            else
                return $this->redirectToRoute("app_account_adress");
        }

        return $this->render('account/adress_form.html.twig', [
            'controller_name' => 'AccountAdressController',
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/account/adress/edit/{id}", name="app_account_adress_edit")
     */
    public function editAdress(Request $reqest, $id): Response
    {
        $repo = $this->doctrine->getRepository(Adress::class);
        $adress = $repo->findOneById($id);

        if (!$adress || $adress->getClient() != $this->getUser())
            throw new Exception("L'id de cette adresse ne correspond à aucune adresse en bdd ou n'appartient pas au user");

        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($reqest);

        if ($form->isSubmitted() && $form->isValid()) {
            $adress->setClient($this->getUser());
            $this->doctrine->persist($adress);
            $this->doctrine->flush();
            return $this->redirectToRoute("app_account_adress");
        }

        return $this->render('account/adress_form.html.twig', [
            'controller_name' => 'AccountAdressController',
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/account/adress/delete/{id}", name="app_account_adress_delete")
     */
    public function deleteAdress(Request $reqest, $id): Response
    {
        $repo = $this->doctrine->getRepository(Adress::class);
        $adress = $repo->findOneById($id);

        if (!$adress || $adress->getClient() != $this->getUser())
            throw new Exception("L'id de cette adresse ne correspond à aucune adresse en bdd ou n'appartient pas au user");

        $this->doctrine->remove($adress);
        $this->doctrine->flush();

        return $this->redirectToRoute('app_account_adress');
    }
}
