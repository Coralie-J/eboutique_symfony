<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adresse;
use App\Repository\CategorieRepository;
use App\Repository\UserRepository;
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
    public function deconnexion(Session $session): Response {
        $session->remove('username');
        $session->remove('useremail');
        $session->remove('userid');
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/update/{id}', name: 'user_update', methods: ['GET','POST'])]
    public function update(User $user, CategorieRepository $categorieRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response {

        if ($request->getMethod() === "POST" ){

            $user->setPrenom($request->request->get('prenom'));
            $user->setNom($request->request->get('nom'));
            $user->setEmail($request->request->get('email'));

            $hashedPassword = $passwordHasher->hashPassword($user, $_POST['password']);
            $user->setPassword($hashedPassword);


            for ($i=0; $i < $user->getAdresses()->count(); $i++){
                $user->getAdresses()[$i]->setVille($request->request->get('ville'.$i));
                $user->getAdresses()[$i]->setCodePostal($request->request->get('code_postal'. $i));
                $user->getAdresses()[$i]->setAdresse($request->request->get('adresse'. $i));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('user/form_update.html.twig', [
            'user' => $user,
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository, Session $session, UserRepository $userRepo): Response
    {

        $user = $userRepo->findOneBy(['email' => $session->get("useremail")]);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'categories' =>  $categorieRepository->findAll()
        ]);
    }



}
