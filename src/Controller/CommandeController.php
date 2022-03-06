<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\User2Repository;
use App\Repository\CategorieRepository;
use App\Entity\CommandeLine;
use App\Entity\Panier;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/commande')]
#[IsGranted("ROLE_USER")]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository, CategorieRepository $categorieRepository, Session $session, User2Repository $userRepo ): Response
    {

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findBy([
                'id_user' => $session->get('userid'),
            ]),
            'user' => $userRepo->find($session->get('userid')),
            'categories' =>  $categorieRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'commande_new', methods: ['POST', 'GET'])]
    public function new(EntityManagerInterface $entityManager, Session $session, User2Repository $userRepo): Response
    {

        if ($session->get("panier")){
            $commande = new Commande();
            $commande->setDate(new \DateTime());
            $commande->setIdUser($userRepo->find($session->get('userid')));

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
    public function show(Commande $commande, CategorieRepository $categorieRepository){

        return $this->render('commande/show.html.twig', [
            'commandeLines' => $commande->getCommandeLines(),
            'commande' => $commande,
            'categories' => $categorieRepository->findAll()
        ]);
    }

}
