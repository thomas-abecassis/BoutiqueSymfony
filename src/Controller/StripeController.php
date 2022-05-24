<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class StripeController extends AbstractController
{
    /**
     * @Route("/stripe/{reference}", name="app_stripe")
     */
    public function index(EntityManagerInterface $doctrine, $reference)
    {

        //On utilise la reference passée en paramètre pour récupérer tous les produits d'une commande
        $order = $doctrine->getRepository(Order::class)->findOneByReference($reference);
        $products = $order->getOrderDetails();


        if (!$order)
            return $this->redirectToRoute('app_home');

        //On ajoute tout les produits dans un tableau formaté pour Stripe
        $productsStripe = [];
        foreach ($products as $product) {
            $productStripe = [];
            $productStripe['price_data'] = [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $product->getProduct(),
                ],
                "unit_amount" => $product->getPrice(),
            ];
            $productStripe['quantity'] = $product->getQuantity();
            $productsStripe[] = $productStripe;
        }

        //Ajout de la livraison en tant que produit 
        $livraison = [
            "price_data" =>
            [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $order->getCarrierName(),
                ],
                "unit_amount" => $order->getDeliveryPrice() * 100,
            ],
            "quantity" => 1
        ];
        $productsStripe[] = $livraison;


        // This is your test secret API key.
        \Stripe\Stripe::setApiKey('sk_test_51L2ZosATxl592yi2egxAMWLgwQEmeFHj7AcY8mKSvldNDjaiuGIWv7SomuDH3vb00eZ4B1TP8D6N5VpxWOVSPX0U00LIacDl9L');

        $YOUR_DOMAIN = 'http://localhost:8000';

        //Encore du formatage pour Stripe et on rajoute bien le tableau de produits créé avant
        $checkout_session = \Stripe\Checkout\Session::create([
            "customer_email" => $this->getUser()->getEmail(),
            'line_items' => $productsStripe,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . 'commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . 'commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);


        $response = new JsonResponse(["id" => $checkout_session->id]);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Location', $checkout_session->url);
        $response->setStatusCode(303);

        return $response;
    }
}
