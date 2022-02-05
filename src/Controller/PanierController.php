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
    public function index(PanierRepository $panierRepository): Response
    {
        return $this->render('panier/index.html.twig', [
            'paniers' => $panierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        $panier = new Panier();

        /*$form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }*/

        return $this->renderForm('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/add', name: 'panier_add', methods: ['GET'])]

    public function add(Request $request, ProduitRepository $produitRepository, PanierRepository $panierRepo, EntityManagerInterface $entityManager, Session $session): Response
    {

        if (! $session->get("panier")){
            $session->set("panier", new Panier());
        }

        $panierLine = new PanierLine();
        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        if ($request->query->get('id_produit')){
            $produit = $produitRepository->find($request->query->get('id_produit'));
            $panierLine->setIdProduit($produit);

            $update_fait = $session->get("panier")->updatePanierLine($panierLine);

            if ($update_fait == null){
                $panierLine->setQuantite(1);
                $session->get("panier")->addPanierLine($panierLine);
            }
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $session->get("panier")->getPanierLines(),
            'categories' => $categories
        ]);
    }

    #[Route('/{id}', name: 'panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
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
