<?php

namespace App\Controller;

use App\Form\EditUserFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditProfilController extends AbstractController
{
    #[Route('/edit/profil/{uuid}', name: 'app_edit_profil')]
    public function index($uuid, UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->findOneBy(['uuid' =>$uuid]);

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        return $this->render('edit_profil/index.html.twig', [
            'form'=> $form->createView(),
            'controller_name' => 'EditProfilController',
        ]);
    }
}
