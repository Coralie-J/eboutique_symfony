<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'categorie_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, ValidatorInterface $validator): Response
    {
       
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        $validation_categorie = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $validation_categorie = $validator->validate($categorie);
            if (count($validation_categorie) == 0){
                $entityManager->persist($categorie);
                $entityManager->flush();
                return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('categorie/new.html.twig', [
            'errors' => $validation_categorie,
            'categorie' => $categorie,
            'categories' => $categorieRepository->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/show_products.html.twig', [
            'categorie' => $categorie,
            'produits' => $categorie->getProduits(),
            'categories'=> $categorieRepository->findAll()
        ]);
    }

    #[Route('/{id}/edit', name: 'categorie_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
