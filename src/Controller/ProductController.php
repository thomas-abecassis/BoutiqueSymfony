<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Produit;
use App\Form\SearchType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }


    /**
     * @Route("/produits", name="app_products")
     */
    public function index(Request $request): Response
    {

        $products = $this->entityManager->getRepository(Produit::class)->findAll();

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);


        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="app_product")
     */
    public function show($slug): Response
    {

        $product = $this->entityManager->getRepository(Produit::class)->findOneBySlug($slug);

        if (!$product)
            return $this->redirectToRoute("app_products");

        return $this->render('product/show.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product
        ]);
    }
}
