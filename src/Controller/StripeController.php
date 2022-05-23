<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
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

        $order = $doctrine->getRepository(Order::class)->findOneByReference($reference);
        if (!$order) {
            return $this->redirectToRoute('app_home');
        }
        $productsStripe = [];
        foreach ($cart->getFull() as $product) {
            $productStripe = [];
            $productStripe['price_data'] = [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $product["product"]->getName(),
                ],
                "unit_amount" => $product["product"]->getPrice(),
            ];
            $productStripe['quantity'] = $product["quantity"];
            $productsStripe[] = $productStripe;
        }


        // This is your test secret API key.
        \Stripe\Stripe::setApiKey('sk_test_51L2ZosATxl592yi2egxAMWLgwQEmeFHj7AcY8mKSvldNDjaiuGIWv7SomuDH3vb00eZ4B1TP8D6N5VpxWOVSPX0U00LIacDl9L');

        $YOUR_DOMAIN = 'http://localhost:8000';

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1L2aeeATxl592yi2ct74rK9E',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);


        $response = new JsonResponse(["id" => $checkout_session->id]);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Location', $checkout_session->url);
        $response->setStatusCode(303);

        return $response;
    }
}
