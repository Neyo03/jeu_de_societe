<?php

namespace App\Controller\Message;

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

class SendMessageController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;        
    }
    
    public function sendMessage(Security $security, DiscussionRepository $discussionRepository, Discussion $discussion): Response
    {
        
        $form = $this->createForm(MessageType::class);
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $request = $this->container->get('request_stack')->getMainRequest();
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid() ) {
           
            if ($discussion != null ) {
            
                $user = $security->getUser();
                 
                $stringMessage = $form->get('content')->getData();
                $message = new Message();
                $message->setContent($stringMessage)->setAuthor($user);
                            
               $discussion->addMessage($message);
                
                $this->entityManager->persist($discussion);
                $this->entityManager->flush();
                
                $this->addFlash('success', "Message successfully sent");
            }else {
               $this->addFlash('error', "The target discussion cannot be found");
            }
            
        }

        return $this->render('send_message/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
}
