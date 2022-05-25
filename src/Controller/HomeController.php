<?php

namespace App\Controller;

use App\Classe\Suivi;
use App\Entity\Produit;
use App\Entity\Carrousel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $bestProducts = $this->entityManager->getRepository(Produit::class)->findByIsBest(true);
        $carrousel = $this->entityManager->getRepository(Carrousel::class)->findAll();
        return $this->render('home/index.html.twig', [
            "products" => $bestProducts,
            "carrousel" => $carrousel
        ]);
    }
}
