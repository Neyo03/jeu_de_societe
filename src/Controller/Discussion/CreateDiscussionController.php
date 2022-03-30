<?php

namespace App\Controller\Discussion;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use App\Form\CreateDiscussionFormType;
use App\Form\MessageType;
use App\Repository\DiscussionParticipantRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CreateDiscussionController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;        
    }
    public function createDiscussion(Security $security, DiscussionRepository $discussionRepository, User $firstParticipant = null): Response
    {
        
        $form = $this->createForm(MessageType::class);
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $request = $this->container->get('request_stack')->getMainRequest();
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid() ) {
           
            if ($firstParticipant != null ) {
            
                $user = $security->getUser();
                $allDiscussionByConnectedUser = $discussionRepository->findBy(['author' => $user]);

                $discussionExist = $this->discussionExist($allDiscussionByConnectedUser, $firstParticipant);
                
                $data = $discussionExist != null ? $discussionExist : new Discussion;
                
                $stringMessage = $form->get('content')->getData();
                $firstMessage = new Message();
                $firstMessage->setContent($stringMessage);
            
                if ($data->getId() === null) {
                    $data->setAuthor($user);
                    
                    $data->addParticipant($user);
                    $data->addParticipant($firstParticipant);
                

                    foreach($data->getParticipants() as $participant){
                        if (in_array($participant, $data->getAllParticipants())) {
                            continue;
                        }
                        $data->addParticipant($participant);
                    };

                    $data->addMessage($firstMessage);
                    foreach ($data->getMessages() as $message ) {
                        $message->setAuthor($user);
                        $allParticipants = $data->getAllParticipants();
                        $message->setAllParticipants($allParticipants);
                    }

                    $this->entityManager->persist($data);
                    $this->entityManager->flush();
                }
                else {
                    $firstMessage->setAuthor($user);
                    $data->addMessage($firstMessage);
                    $this->entityManager->persist($data);
                    $this->entityManager->flush();
                }
                $this->addFlash('success', "Message successfully sent");
            }else {
               $this->addFlash('error', "The target user cannot be found");
            }
            
        }

        return $this->render('create_discussion/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function discussionExist($allDiscussionByConnectedUser,$participant) {
       
        
        foreach ($allDiscussionByConnectedUser as $discussionByUser) {
                    
            if ($discussionByUser->isParticipantInDiscussion($participant)) {
                $data = $discussionByUser;
            }
        }
        return $data;
    }
    
}
