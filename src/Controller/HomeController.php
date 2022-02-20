<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(CategorieRepository $categorieRepository, ProduitRepository $produitRepository): Response
    {

        return $this->render('home/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'produits' => $produitRepository->findAll()
        ]);
    }
}
