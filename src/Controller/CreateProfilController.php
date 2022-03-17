<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateProfilController extends AbstractController
{
    #[Route('/create/profil', name: 'app_create_profil')]
    public function index(): Response
    {
        return $this->render('create_profil/index.html.twig', [
            'controller_name' => 'CreateProfilController',
        ]);
    }
}
