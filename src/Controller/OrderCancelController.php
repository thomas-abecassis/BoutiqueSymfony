<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $doctrine;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->doctrine = $entityManager;
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="app_order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->doctrine->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getClient() != $this->getUser())
            return $this->redirectToRoute('app_home');

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order,
        ]);
    }
}
