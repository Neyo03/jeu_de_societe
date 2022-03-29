<?php

namespace App\Controller\User;

use App\Entity\ValidationUser;
use App\Repository\UserRepository;
use App\Repository\ValidationUserRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ResendEmailValidationController extends AbstractController
{

    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    #[Route('/resend/email/validation', name: 'app_resend_email_validation')]
    public function resendEmail(Request $request,UserRepository $userRepository, Security $security, ValidationUserRepository $validationUserRepository,EntityManagerInterface $entityManager): Response
    {
        
        $uuid = $security->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['uuid' => $uuid]);

        if ($user === null ) {
            throw new Exception('There is an error in your mail', 1);
        }
        if ($user->getIsEnabled()===true) {
            throw new Exception('Your account is already validated', 1);
        }
    
        $newToken = new ValidationUser();

        $newToken->generateValidationToken($user);
        // $oldToken = $validationUserRepository->findOneBy(["tokenValidation" => $user->getValidationUsers()->getValues()]);
    
        $entityManager->persist($newToken);
        $entityManager->flush();

        $subject = "Validation your account";
        $template = "email/valid-user.html.twig";

        $this->mailer->send($user,$subject,$template, [
            'tokenValidation' => $newToken->getTokenValidation()
        ]);

        $this->addFlash('success', 'Email is successfully resent, please check your mailbox for actived your account');

        return $this->redirectToRoute("app_edit_profil", ["uuid"=>$user->getUuid()]);
    }
}
