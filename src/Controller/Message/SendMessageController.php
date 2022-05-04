<?php

namespace App\Controller\Message;

use App\Entity\Discussion;
use App\Entity\DiscussionParticipant;
use App\Entity\Message;
use App\Entity\MessageParticipant;
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
    private $discussionRepository;
    private $security;
    private $discussionParticipantRepository;

    public function __construct(EntityManagerInterface $entityManager, DiscussionRepository $discussionRepository, Security $security, DiscussionParticipantRepository $discussionParticipantRepository)
    {
        $this->entityManager = $entityManager;       
        $this->discussionRepository = $discussionRepository;     
        $this->security = $security;     
        $this->discussionParticipantRepository = $discussionParticipantRepository;

    }
    
    public function sendMessageForm(Security $security, DiscussionRepository $discussionRepository, Discussion $discussion): Response
    {
        
        $form = $this->createForm(MessageType::class);
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $request = $this->container->get('request_stack')->getMainRequest();
        $form->handleRequest($request);
    
        return $this->render('send_message/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/send/message/discussion', name: 'app_send_message_in_discussion')]
    public function sendMessage()
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $request = $this->container->get('request_stack')->getMainRequest();
        
        $uuid = $request->request->all()[0];
        $stringMessage =$request->request->all()[1];
        $discussion =  $this->discussionRepository->findOneBy(['uuid' => $uuid ]);
      
           
        if ($discussion != null ) {
        
            $user = $this->security->getUser();
                
            $message = new Message();
            $message->setContent($stringMessage)->setAuthor($user);

            foreach ($discussion->getAllParticipants() as $participant ) {
                $messageParticipant = new MessageParticipant();
                $messageParticipant->setMessage($message)
                ->setParticipant($participant);
                $this->entityManager->persist($messageParticipant);
                
            }

            $discussionStatus = $this->discussionParticipantRepository->getStatutByDiscussionAndExcludeCurrentUser($discussion, $user);
           
            foreach ($discussionStatus as $status) {
                $status->setStatus(DiscussionParticipant::NOT_READ);
                $this->entityManager->persist($status);

            }

            $discussion->addMessage($message);
            
            $this->entityManager->persist($discussion);
            $this->entityManager->flush();
            
            return $this->json($message->toArray());
        }else {
            return new Response('', 500);
        }

        
            
    }
    
    
}
