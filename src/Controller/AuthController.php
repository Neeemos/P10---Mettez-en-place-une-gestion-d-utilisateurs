<?php

namespace App\Controller;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\AuthType;
use Doctrine\ORM\EntityManagerInterface;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(request $request): Response
    {
        $form = $this->createForm(AuthType::class, null, ['is_registration' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //// auth
        }
        return $this->render('./auth/login.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'controller_name' => 'AuthControllerRegister',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_auth')]
    public function login(request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AuthType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          //// auth
        }
        return $this->render('./auth/login.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'controller_name' => 'AuthControllerLogin',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/landing', name: 'app_dispatch')]
    public function dispatch(request $request): Response
    {
        return $this->render('./auth/landing.html.twig', [
            'current_route' => $request->attributes->get('_route'),
            'controller_name' => 'AuthController',
        ]);
    }
}
