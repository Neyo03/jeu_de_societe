<?php

namespace App\Controller;

use App\Entity\ValidationUser;
use App\Repository\ValidationUserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValidUserAccountController extends AbstractController
{
    #[Route('/valid/account/{tokenValidation}', name: 'app_valid_user_account')]
    public function index($tokenValidation,ValidationUserRepository $validationUserRepository,EntityManagerInterface $entityManagerInterface ): Response
    {
     
        $user = $validationUserRepository->findOneBy(["tokenValidation"=>$tokenValidation ])->getUser();
    
        if ($user === null ) {
            throw new Exception("Your token is not valid", 1);   
        }
        if ($user->getIsEnabled()) {
            throw new Exception("Your account is already enabled", 1);   
        }
        if ($user->getValidationUsers()->getValues()[0]->getTokenValidationExpiredAt() < new DateTime()) {
            throw new Exception("Token is expirated, please retry", 1);   
        }

        $user->setIsEnabled(true);
        $user->setRoles(["ROLE_ENABLED"]);

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Your account has been successfully enabled');

        return $this->redirectToRoute("app_edit_profil", ["uuid"=>$user->getUuid()]);
    }
}
