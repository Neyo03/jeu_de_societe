<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateDiscussionController extends AbstractController
{
    #[Route('/create/discussion', name: 'app_create_discussion')]
    public function createDiscussion(): Response
    {
        

        return $this->render('create_discussion/index.html.twig', [
            'controller_name' => 'CreateDiscussionController',
        ]);
    }
}
