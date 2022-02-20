<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adresse;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/user')]
class UserController extends AbstractController{

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        if ($request->getMethod() === 'POST'){
            $user->setNom($_POST['nom']);
            $user->setEmail($_POST['email']);

            $hashedPassword = $passwordHasher->hashPassword($user,$_POST['password']);
            $user->setPassword($hashedPassword);
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

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('user/form_create.html.twig', [
            'user' => $user,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/login', name: 'user_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, CategorieRepository $categorieRepository): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'categories' => $categorieRepository->findAll(),
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'user_logout', methods: ['GET', 'POST'])]
    public function deconnexion(Session $session): void {
        $session->remove('username');
        $session->remove('id');
    }

}
