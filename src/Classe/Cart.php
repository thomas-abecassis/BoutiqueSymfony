<?php

namespace App\Classe;

use Exception;
use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, ManagerRegistry $doctrine)
    {
        $this->session = $session;
        $this->entityManager = $doctrine->getManager();
    }


    /**
     * return associative array[id=>quantity]
     * @return array
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    /**
     * return associative array["quantity" => int,
     *                          "product"  => productObject]
     * @return array
     */
    public function getFull()
    {
        $cart = $this->get();
        $cartComplete = [];
        $repo = $this->entityManager->getRepository(Produit::class);

        foreach ($cart as $id => $quantity) {
            $product = $repo->findOneById($id);
            $cartComplete[] = [
                "product" => $product,
                "quantity" => $quantity
            ];
        }

        return $cartComplete;
    }

    /**
     * Add one new product to cart
     */
    public function add($id)
    {
        //Check que l'id envoyé correspond bien à un produit
        $repo = $this->entityManager->getRepository(Produit::class);
        $product = $repo->findOneById($id);
        if (!$product)
            throw new Exception("L'id donné ne correspond à aucun produit en bdd");


        $cart = $this->session->get("cart");

        if (!empty($cart[$id]))
            $cart[$id]++;
        else
            $cart[$id] = 1;

        $this->session->set("cart", $cart);
    }

    /**
     * Delete all the products from the cart
     */
    public function remove()
    {
        return $this->session->remove('cart');
    }

    /**
     * Remove a product from cart (ignore quantity)
     **/
    public function delete($id)
    {
        $cart = $this->get();

        unset($cart[$id]);

        $this->session->set("cart", $cart);
    }

    /**
     * Remove a product from cart (one quantity)
     */
    public function decrease($id)
    {
        $cart = $this->get();
        if (!empty($cart[$id])) {
            if (--$cart[$id] == 0) {
                $this->delete($id);
                return;
            }
        }


        $this->session->set("cart", $cart);
    }
}
