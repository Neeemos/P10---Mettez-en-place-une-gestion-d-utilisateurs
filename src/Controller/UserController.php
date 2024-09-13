<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_list')]
    public function index(request $request, UserRepository $userRepository): Response
    {
        $userList = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'user_list' => $userList
        ]);
    }
    #[Route('/user/{id}/edit', name: 'user_edit',  methods: ['GET', 'POST'])]
    public function edit(request $request, User $user , EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    #[Route('/user/{id}/remove', name: 'user_remove',  methods: ['POST', 'GET'])]
    public function remove(request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        foreach ($user->getTasks() as $task) {
            $task->setUser(null);
            $entityManager->persist($task);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_list');
    }
}
