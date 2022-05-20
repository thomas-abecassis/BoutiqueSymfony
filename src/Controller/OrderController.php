<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="app_order")
     */
    public function index(Request $request): Response
    {

        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute("app_account_adress_add", ["cart" => "panier"]);
        }

        $form = $this->createForm(OrderType::class, null, ["user" => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render('order/index.html.twig', ["form" => $form->createView()]);
    }
}
