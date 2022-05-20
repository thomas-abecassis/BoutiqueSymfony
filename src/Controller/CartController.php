<?php

namespace App\Controller;


use Exception;
use App\Classe\Cart;
use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/mon-panier", name="app_cart")
     */
    public function index(Cart $cart): Response
    {

        return $this->render('cart/index.html.twig', [
            "cart" => $cart->getFull()
        ]);
    }

    /**
     * @Route("/ajout-panier/{id}", name="add_cart")
     */
    public function add(Cart $cart, $id)
    {

        $cart->add($id);


        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/supr-panier/{id}", name="delete_cart")
     */
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/dim-panier/{id}", name="decrease_cart")
     */
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/supr-panier", name="remove_cart")
     */
    public function remove(Cart $cart)
    {
        $cart->remove();
        return $this->redirectToRoute('app_cart');
    }
}
