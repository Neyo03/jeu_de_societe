<?php

namespace App\Controller;

use App\Form\EditUserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EditProfilController extends AbstractController
{
    #[Route('/edit/profil/{uuid}', name: 'app_edit_profil')]
    public function edit($uuid, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {

        $user = $userRepository->findOneBy(['uuid' =>$uuid]);

        if ($user !== $security->getUser()) {
            return $this->redirectToRoute("app_home");  
        }

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() &&  $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

           
        }

        return $this->render('edit_profil/index.html.twig', [
            'form'=> $form->createView(),
            'controller_name' => 'EditProfilController',
        ]);
    }
}
