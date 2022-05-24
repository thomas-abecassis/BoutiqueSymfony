<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{

    private $doctrine;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->doctrine = $entityManager;
    }


    /**
     * @Route("/compte/commandes", name="app_account_orders")
     */
    public function commandes(): Response
    {
        $orders = $this->doctrine->getRepository(Order::class)->findAllPaidsOrders($this->getUser());
        return $this->render('account/orders.html.twig', [
            "orders" => $orders
        ]);
    }

    /**
     * @Route("/compte/commande/{reference}", name="app_account_order")
     */
    public function commande($reference): Response
    {
        $order = $this->doctrine->getRepository(Order::class)->findOneByReference($reference);

        return $this->render('account/order.html.twig', [
            "order" => $order
        ]);
    }
}
