<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\PanierLine;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

#[Route('/panier')]
class PanierController extends AbstractController
{

    #[Route('/', name: 'panier_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository, Session $session): Response
    {
        if (! $session->get("panier")){
            $session->set("panier", new Panier());
        }

        return $this->render('panier/index.html.twig', [
             'panier' => $session->get("panier")->getPanierLines(),
             'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/add', name: 'panier_add', methods: ['GET'])]

    public function add(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, Session $session): Response
    {

        if (! $session->get("panier")){
            $session->set("panier", new Panier());
        }

        $panierLine = new PanierLine();

        if ($request->query->get('id_produit')){
            $produit = $produitRepository->find($request->query->get('id_produit'));
            $panierLine->setIdProduit($produit);

            $panierLineInProduit = $session->get("panier")->updatePanierLine($panierLine);

            if ($panierLineInProduit == null){
                $panierLine->setQuantite(1);
                $session->get("panier")->addPanierLine($panierLine);
            }
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/update', name: 'panier_update', methods: ['POST'])]
    public function update(Session $session, Request $request) : Response
    {

        foreach($session->get("panier")->getPanierLines() as $panierline){
            foreach ($_POST as $key => $value){
                if ($panierline->getQuantite() != $value && $panierline->getIdProduit()->getId() == $key){
                    $panierline->setQuantite($value);
                }
            }
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/', name: 'panier_remove', methods: ['GET'])]
    public function remove(Session $session, Request $request){
        if ($request->query->get('id_produit')){
            $session->get("panier")->removePanierLineByProduit($request->query->get('id_produit'));
        }
        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }
}