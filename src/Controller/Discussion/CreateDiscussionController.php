<?php

namespace App\Controller\Discussion;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use App\Form\CreateDiscussionFormType;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function createDiscussion(Security $security, User $firstParticipant = null): Response
    {

        $data = new Discussion();
        $user = $security->getUser();
        $form = $this->createForm(MessageType::class);

        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $request = $this->container->get('request_stack')->getMainRequest();
        $form->handleRequest($request);
       
        if ($form->isSubmitted()) {
            

            $stringMessage = $form->get('content')->getData();
            $firstMessage = new Message();
            $firstMessage->setContent($stringMessage);
        
            if ($data->getId() === null && $firstParticipant != null ) {
                $data->setAuthor($user);
                
                $data->addParticipant($user);
                $data->addParticipant($firstParticipant);
                $data->addMessage($firstMessage);

                foreach($data->getParticipants() as $participant){
                    if (in_array($participant, $data->getAllParticipants())) {
                        continue;
                    }
                    $data->addParticipant($participant);
                };
                foreach ($data->getMessages() as $message ) {
                    $message->setAuthor($user);
                    $allParticipants = $data->getAllParticipants();
                    $message->setAllParticipants($allParticipants);
                }

                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }
            
        }

        return $this->render('create_discussion/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
