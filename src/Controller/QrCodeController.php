<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EmployeRepository;
use App\Form\EmployeType;
use App\Form\RegisterType;
use App\Entity\Employe;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleAuthenticatorTwoFactorInterface;
use Endroid\QrCode\ErrorCorrectionLevel as QrErrorCorrectionLevel;


class QrCodeController extends AbstractController
{
    #[Route('/2fa/qrcode', name: '2fa_qrcode')]
    public function displayGoogleAuthenticatorQrCode(GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager): Response
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($googleAuthenticator->getQRContent($this->getUser())) // Utilise l'utilisateur authentifié
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(QrErrorCorrectionLevel::High)
            ->size(200)
            ->margin(0)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }

    #[Route('/2fa', name: '2fa_login')]
    public function displayGoogleAuthenticator(GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_security_login'); // Redirigez vers la page de connexion
        }

        // Vérifiez que l'utilisateur implémente GoogleAuthenticatorTwoFactorInterface
        if (!$user instanceof GoogleAuthenticatorTwoFactorInterface) {
            throw new \LogicException('L\'utilisateur doit implémenter GoogleAuthenticatorTwoFactorInterface.');
        }

        // Si l'utilisateur n'a pas encore de secret, en générer un
        if (!$user->getGoogleAuthenticatorSecret()) {
            $secret = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticator($secret);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('security/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qrcode'),
        ]);
    }
    #[Route('/2fa/check', name: '2fa_check', methods: ['POST'])]
    public function checkGoogleAuthenticator(Request $request, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        $user = $this->getUser();

        if (!$user instanceof GoogleAuthenticatorTwoFactorInterface) {
            throw new \LogicException('L\'utilisateur doit implémenter GoogleAuthenticatorTwoFactorInterface.');
        }

        $submittedCode = $request->request->get('2fa_code');

        // Vérifiez le code 2FA
        if ($googleAuthenticator->checkCode($user, $submittedCode)) {
            // Authentifiez l'utilisateur
            return $this->redirectToRoute('project_index'); // Remplacez par la route souhaitée
        }

        // Si le code est incorrect, redirigez avec un message d'erreur
        return $this->render('security/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qrcode'),
            'error' => 'Le code est incorrect. Veuillez réessayer.',
        ]);
    }

}