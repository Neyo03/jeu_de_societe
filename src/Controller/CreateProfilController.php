<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ValidationUser;
use App\Form\RegistrationFormType;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Rfc4122\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateProfilController extends AbstractController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
           
            $user->eraseCredentials();
            $entityManager->persist($user);

            $validationUser = new ValidationUser();
            $validationUser->generateValidationToken($user);
            $user->addValidationUser($validationUser);


            $entityManager->flush();

        
            $subject = "Registration Mail";
            $template = 'email/registration.html.twig';
            $datasMail = ["tokenValidation" => $user->getValidationUsers()->getValues()[0]] ;

            $this->mailer->send($user,$subject,$template,$datasMail);

            $this->addFlash('success', 'Your account has been successfully created, please check your mailbox for actived your account');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('create_profil/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
