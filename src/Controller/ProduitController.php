<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'produit_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {

        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, CategorieRepository $categorieRepository): Response
    {
        $produit = new Produit();
        $validation_produit = [];

        if ($request->getMethod() === "POST"){
            $categorie = $categorieRepository->findOneBy(["nom" => $request->request->get('type_categorie')]);

            $description = $request->request->get("description");

            $produit->setDescription(trim($description));
            $produit->setInterprete($request->request->get("interprete"));
            $produit->setNom($request->request->get("nom"));

            if (is_numeric($request->request->get("prix"))){
                $produit->setPrixUnitaire($request->request->get("prix"));
            }

            $produit->setTypeCategorie($categorie);
            $produit->setDisponibilite($request->request->get("disponibilite") == "dispo" ? true : false );

            $media = new Media();
            $media->setAlt($request->request->get("alt"));
            $media->setSource($request->request->get("source"));
            $media->setIdProduit($produit);
            $produit->setMedia($media);

            $validation_produit = $validator->validate($produit);
            $validation_produit->addAll($validator->validate($media));

            if (count($validation_produit) == 0 ){
                $entityManager->persist($produit);
                $entityManager->persist($media);
                $entityManager->flush();

                return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('produit/form.html.twig', [
            'errors' => $validation_produit,
            'produit' => $produit,
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'produit_show', methods: ['GET'])]
    public function show(Produit $produit, CategorieRepository $categorieRepository): Response
    {

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {

        if ($request->getMethod() === "POST"){
            $categorie = $categorieRepository->find($request->request->get('type_categorie'));

            $produit->setDescription($request->request->get("description"));
            $produit->setInterprete($request->request->get("interprete"));
            $produit->setNom($request->request->get("nom"));
            $produit->setPrixUnitaire($request->request->get("prix"));
            $produit->setTypeCategorie($categorie);
            $produit->setDisponibilite($request->request->get("disponibilite") == "dispo" ? true : false);
            
            $media = $produit->getMedia();
            $produit->getMedia()->setAlt($request->request->get("alt_" . strval($media->getId())));
            $produit->getMedia()->setSource($request->request->get("source_" . strval($media->getId())));
            $entityManager->persist($media);
            
            
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);

        }
        

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'produit_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit->getMedia());
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
