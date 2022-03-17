<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditProfilController extends AbstractController
{
    #[Route('/edit/profil', name: 'app_edit_profil')]
    public function index(): Response
    {
        return $this->render('edit_profil/index.html.twig', [
            'controller_name' => 'EditProfilController',
        ]);
    }
}
