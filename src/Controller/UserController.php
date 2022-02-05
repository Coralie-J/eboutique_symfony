<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Adresse;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

#[Route('/user')]
class UserController extends AbstractController{

    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        $req_produits = $entityManager->createQuery('SELECT p FROM App\Entity\Produit p');
        $produits = $req_produits->getResult();

        return $this->render('user/default.html.twig', [
            'categories' => $categories,
            'produits' => $produits
        ]);

    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        if ($request->getMethod() === 'POST'){
            $user->setNom($_POST['nom']);
            $user->setEmail($_POST['email']);
            $user->setPassword(hash('sha256',$_POST['password']));
            $user->setLogin($_POST['login']);
            $user->setPrenom($_POST['prenom']);
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

        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        return $this->render('user/form.html.twig', [
            'user' => $user,
            'categories' => $categories,
        ]);
    }

    #[Route('/connexion', name: 'user_connexion', methods: ['GET', 'POST'])]
    public function connexion(Request $request, EntityManagerInterface $entityManager, Session $session): Response
    {
        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();
        $info = '';

        if ($request->getMethod() === 'POST'){
            $id = $entityManager->createQuery('SELECT u.id, u.prenom FROM App\Entity\User u
                 WHERE u.login = :login AND u.password = :password')
                    ->setParameters(array(
                        'login' => $_POST['login'],
                        'password' => hash('sha256',$_POST['password'])
                    ));

            $id_result = $id->getResult();

            if ($id_result){
                $session->set('id', $id_result[0]['id']);
                $session->set('username', $id_result[0]['prenom']);
                return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $info = 'Indentifiants incorrects';
            }
        }

        return $this->render('user/form_login.html.twig', [
            'categories' => $categories,
            'info' => $info
        ]);
    }

    #[Route('/deconnexion', name: 'user_deconnexion', methods: ['GET', 'POST'])]
    public function deconnexion(Session $session): Response {
        $session->remove('username');
        $session->remove('id');
        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

}
