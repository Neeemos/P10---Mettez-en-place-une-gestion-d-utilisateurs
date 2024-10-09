<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
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
            return $this->redirectToRoute('app_security_login');
        }

        // Vérifiez que l'utilisateur implémente GoogleAuthenticatorTwoFactorInterface
        if (!$user instanceof GoogleAuthenticatorTwoFactorInterface) {
            throw new \LogicException('L\'utilisateur doit implémenter GoogleAuthenticatorTwoFactorInterface.');
        }

        // Générer le QR code, même si le secret existe déjà
        return $this->render('security/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qrcode'),
        ]);
    }




}