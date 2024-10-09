<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrationFormType;
use App\Entity\User;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;

class SecurityController extends AbstractController
{
    
    #[Route('/landing', name: 'app_security_dispatch')]
    public function dispatch(request $request): Response
    {
        return $this->render('./auth/landing.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route(path: '/login', name: 'app_security_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'controller_name' => 'AuthControllerLogin',
            'error' => $error,
        ]);
    }


    #[Route('/register', name: 'app_security_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, GoogleAuthenticatorInterface $googleAuth): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
    
            $secret = $googleAuth->generateSecret();
            $user->setGoogleAuthenticator($secret);
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Rediriger vers la page d'activation de la 2FA
            return $this->redirectToRoute('2fa_login');
        }
    
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
