<?php

namespace App\Controller;

use App\Entity\PanierLine;
use App\Form\PanierLineType;
use App\Repository\PanierLineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier/line')]
class PanierLineController extends AbstractController
{
    #[Route('/', name: 'panier_line_index', methods: ['GET'])]
    public function index(PanierLineRepository $panierLineRepository): Response
    {
        return $this->render('panier_line/index.html.twig', [
            'panier_lines' => $panierLineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'panier_line_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panierLine = new PanierLine();
        $form = $this->createForm(PanierLineType::class, $panierLine);
        $form->handleRequest($request);

        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panierLine);
            $entityManager->flush();

            return $this->redirectToRoute('panier_line_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier_line/new.html.twig', [
            'panier_line' => $panierLine,
            'form' => $form,
            'categories' => $categories
        ]);
    }

    #[Route('/{id}', name: 'panier_line_show', methods: ['GET'])]
    public function show(PanierLine $panierLine): Response
    {
        return $this->render('panier_line/show.html.twig', [
            'panier_line' => $panierLine,
        ]);
    }

    #[Route('/{id}/edit', name: 'panier_line_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PanierLine $panierLine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierLineType::class, $panierLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('panier_line_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier_line/edit.html.twig', [
            'panier_line' => $panierLine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'panier_line_delete', methods: ['POST'])]
    public function delete(Request $request, PanierLine $panierLine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panierLine->getId(), $request->request->get('_token'))) {
            $entityManager->remove($panierLine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_line_index', [], Response::HTTP_SEE_OTHER);
    }
}
