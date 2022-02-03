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

#[Route('/user')]
class UserController extends AbstractController{

    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();

        $req_produits = $entityManager->createQuery('SELECT p FROM App\Entity\Produit p');
        $produits = $req_produits->getResult();

        return $this->renderForm('user/default.html.twig', [
            'categories' => $categories,
            'produits' => $produits
        ]);

    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        // $form = $this->createForm(UserType::class, $user);
        // $form->handleRequest($request);

        if ($request->getMethod() === 'POST'){
            $user->setNom($_POST['nom']);
            $user->setEmail($_POST['adresse']);
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

        /*if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(hash('sha256', $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }*/

        return $this->renderForm('user/form.html.twig', [
            'user' => $user,
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/login', name: 'user_login', methods: ['GET', 'POST'])]
    public function doLogin(EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() === 'POST'){


        }

        $req = $entityManager->createQuery('SELECT c FROM App\Entity\Categorie c');
        $categories = $req->getResult();


        return $this->render('user/form.login.html.twig', [
            'user' => $user,
            'categories' => $categories
        ]);
    }
}
