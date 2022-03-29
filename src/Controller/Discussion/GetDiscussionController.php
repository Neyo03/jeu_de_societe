<?php

namespace App\Controller\Discussion;

use App\Repository\DiscussionParticipantRepository;
use App\Repository\DiscussionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GetDiscussionController extends AbstractController
{

    #[Route('/get/discussion', name: 'app_get_discussion')]
    public function getAllDiscussion(DiscussionParticipantRepository $discussionParticipantRepository, Security $security): Response
    {

        $user = $security->getUser();

        $allDiscussionByConnectedUser = $discussionParticipantRepository->findBy(['participant' => $user]);
        
        return $this->render('get_discussion/allDiscussion.html.twig', [
            'allDiscussionByConnectedUser' => $allDiscussionByConnectedUser,
        ]);
    }

    #[Route('/get/discussion/{uuid}', name: 'app_get_one_discussion')]
    public function getOneDiscussion($uuid,DiscussionRepository $discussionRepository, Security $security): Response
    {

        $user = $security->getUser();
        $OneDiscussionByUuid = $discussionRepository->findOneBy(['uuid' => $uuid]);
       
        if (!in_array($user, $OneDiscussionByUuid->getAllParticipants())) return $this->redirectToRoute("app_home");

        return $this->render('get_discussion/oneDiscussion.html.twig', [
            'OneDiscussionByUuid' => $OneDiscussionByUuid,
        ]);
    }
}
