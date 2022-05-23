<?php

namespace App\Controller;


use App\Classe\Cart;
use App\Entity\User;
use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/commande", name="app_order")
     */
    public function index(Request $request, Cart $cart): Response
    {
        //si l'utilisateur n'a pas de panier -> redirection vers son panier vide
        if (count($cart->get()) == 0) {
            return $this->redirectToRoute("app_cart");
        }
        //si l'utilisateur n'a pas d'adresse -> redirection vers l'ajout d'adresse
        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute("app_account_adress_add", ["redirection" => "panier"]);
        }

        $form = $this->createForm(OrderType::class, null, ["user" => $this->getUser()]);

        return $this->render('order/index.html.twig', [
            "form" => $form->createView(),
            "cart" => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="app_order_recap", methods={"POST"})
     */
    public function add(Request $request, Cart $cart): Response
    {

        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute("app_account_adress_add", ["redirection" => "commande"]);
        }

        $form = $this->createForm(OrderType::class, null, ["user" => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTimeImmutable();
            $carriers = $form->get("carriers")->getData();
            $delivery = $form->get("delivery")->getData();
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname() . " " . $delivery->getPhone();
            if ($delivery->getCompany())
                $delivery_content .= '<br>' . $delivery->getCompany();
            $delivery_content .= '<br>' . $delivery->getAdresse();
            $delivery_content .= '<br>' . $delivery->getPostal() . ' - ' . $delivery->getCity();
            $delivery_content .= '<br>' . $delivery->getCountry();

            $order = new Order();
            $order->setClient($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setDeliveryPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            $productsStripe = [];
            foreach ($cart->getFull() as $product) {
                $productStripe = [];
                $orderDetail = new OrderDetail();
                $orderDetail->setMyOrder($order);
                $orderDetail->setProduct($product["product"]->getName());
                $orderDetail->setQuantity($product["quantity"]);
                $orderDetail->setPrice($product["product"]->getPrice());
                $orderDetail->setTotal($product["product"]->getPrice() * $product["quantity"]);
                $this->entityManager->persist($orderDetail);
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

            $this->entityManager->flush();

            \Stripe\Stripe::setApiKey('sk_test_51L2ZosATxl592yi2egxAMWLgwQEmeFHj7AcY8mKSvldNDjaiuGIWv7SomuDH3vb00eZ4B1TP8D6N5VpxWOVSPX0U00LIacDl9L');

            $YOUR_DOMAIN = 'http://localhost:8000/public';
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => $productsStripe,
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            ]);

            return $this->render('order/add.html.twig', [
                "cart" => $cart->getFull(),
                "carrier" => $carriers,
                "delivery" => $delivery_content
            ]);
        }

        return $this->redirectToRoute("app_cart");
    }
}
