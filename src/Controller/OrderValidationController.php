<?php

namespace App\Controller;

use Mail;
use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderValidationController extends AbstractController
{

    private $doctrine;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->doctrine = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="app_order_validation")
     */
    public function index($stripeSessionId, Cart $cart): Response
    {

        $order = $this->doctrine->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getClient() != $this->getUser())
            return $this->redirectToRoute('app_home');

        if (!$order->isIsPaid()) {
            $cart->remove();
            $order->setIsPaid(true);
            $this->doctrine->flush();
            $mail = new Mail();
            $mail->send($this->getUser()->getEmail(), "Commande confirmée", "Merci pour votre commande  " . $this->getUser()->getFirstName() . " !");
        }

        return $this->render('order_validation/index.html.twig', [
            'order' => $order,
        ]);
    }
}
