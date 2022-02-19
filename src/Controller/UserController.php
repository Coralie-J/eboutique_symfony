<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adresse;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/user')]
class UserController extends AbstractController{

    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository, ProduitRepository $produitRepository): Response
    {

        return $this->render('user/default.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'produits' => $produitRepository->findAll()
        ]);

    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        if ($request->getMethod() === 'POST'){
            $user->setNom($_POST['nom']);
            $user->setEmail($_POST['email']);

            $hashedPassword = $passwordHasher->hashPassword($user,$_POST['password']);
            $user->setPassword($hashedPassword);

            $user->setLogin($_POST['login']);
            $user->setPrenom($_POST['prenom']);
            $user->setRoles(array('ROLE_USER'));
            $adress = new Adresse();
            $adress->setAdresse($_POST['adresse']);
            $adress->setCodePostal($_POST['code_postal']);
            $adress->setVille($_POST['ville']);
            $user->addAdress($adress);
            $entityManager->persist($user);
            $entityManager->persist($adress);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('user/form.html.twig', [
            'user' => $user,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/deconnexion', name: 'user_deconnexion', methods: ['GET', 'POST'])]
    public function deconnexion(Session $session): Response {
        $session->remove('username');
        $session->remove('id');
        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

}
