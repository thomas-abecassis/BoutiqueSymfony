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
     * Ajout d'une commande en base de donnée (non payée)
     */
    public function add(Request $request, Cart $cart): Response
    {

        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute("app_account_adress_add", ["redirection" => "commande"]);
        }

        $form = $this->createForm(OrderType::class, null, ["user" => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On met en place tout les champs dont on va avoir besoin pour Order et OrderDetail (c'est long)
            $date = new DateTimeImmutable();

            $reference = $date->format('Y-m-d') . '-' . uniqid();

            $carriers = $form->get("carriers")->getData();
            $delivery = $form->get("delivery")->getData();

            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname() . " " . $delivery->getPhone();
            if ($delivery->getCompany())
                $delivery_content .= '<br>' . $delivery->getCompany();
            $delivery_content .= '<br>' . $delivery->getAdresse();
            $delivery_content .= '<br>' . $delivery->getPostal() . ' - ' . $delivery->getCity();
            $delivery_content .= '<br>' . $delivery->getCountry();

            //On créer et persist un objet Order (Commande génerale)
            $order = new Order();

            $order->setClient($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setDeliveryPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);
            $order->setReference($reference);
            //On hardcode un numéro de suivi pour les tests
            $order->setShippingNumber("CB662173705US");

            $this->entityManager->persist($order);

            //On créer et persist des Objects orderDetail (chaque type de produit différent dans une commande)
            foreach ($cart->getFull() as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setMyOrder($order);
                $orderDetail->setProduct($product["product"]->getName());
                $orderDetail->setQuantity($product["quantity"]);
                $orderDetail->setPrice($product["product"]->getPrice());
                $orderDetail->setTotal($product["product"]->getPrice() * $product["quantity"]);
                $this->entityManager->persist($orderDetail);
            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                "cart" => $cart->getFull(),
                "carrier" => $carriers,
                "delivery" => $delivery_content,
                "reference" => $reference
            ]);
        }

        return $this->redirectToRoute("app_cart");
    }
}
