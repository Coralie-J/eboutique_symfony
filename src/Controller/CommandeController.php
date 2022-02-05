<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\UserRepository;
use App\Entity\CommandeLine;
use App\Entity\Panier;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository, EntityManagerInterface $entityManager, Session $session, UserRepository $userRepo ): Response
    {
        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findBy([
                'id_user' => $session->get('id'),
            ]),
            'user' => $userRepo->find($session->get('id')),
            'categories' => $categories
        ]);
    }

    #[Route('/new', name: 'commande_new', methods: ['POST', 'GET'])]
    public function new(EntityManagerInterface $entityManager, Session $session, UserRepository $userRepo): Response
    {

        if (! $session->get('username')){
            return $this->redirectToRoute('user_connexion', [], Response::HTTP_SEE_OTHER);
        }

        if ($session->get("panier")){
            $commande = new Commande();
            $commande->setDate(new \DateTime());
            $commande->setIdUser($userRepo->find($session->get('id')));

            foreach($session->get("panier")->getPanierLines() as $panierline){

                $commandeLine = new CommandeLine();
                $commandeLine->setQuantite($panierline->getQuantite());
                $commandeLine->setIdProduit($panierline->getIdProduit());
                $commandeLine->setIdCommande($commande);
                $entityManager->merge($commandeLine);
            }

            $entityManager->persist($commande);
            $entityManager->flush();

            $session->remove("panier");
            $session->set("panier", new Panier());
        }

        return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show/{id}', name: 'commande_show', methods: ['GET'])]
    public function show(Commande $commande, EntityManagerInterface $entityManager){
        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        return $this->render('commande/show.html.twig', [
            'commandeLines' => $commande->getCommandeLines(),
            'categories' => $categories
        ]);
    }

}
