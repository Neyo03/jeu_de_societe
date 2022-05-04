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
use App\Repository\MessageParticipantRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use function PHPUnit\Framework\throwException;

class ReadMessageController extends AbstractController
{

    private $entityManager;
    private $messageRepository;
    private $discussionRepository;
    private $security;
    private $discussionParticipantRepository;
    private $messageParticipantRepository;
  

    public function __construct(EntityManagerInterface $entityManager, MessageRepository $messageRepository, DiscussionRepository $discussionRepository, Security $security, DiscussionParticipantRepository $discussionParticipantRepository, MessageParticipantRepository $messageParticipantRepository )
    {
        $this->entityManager = $entityManager;       
        $this->messageRepository = $messageRepository;       
        $this->discussionRepository = $discussionRepository;  
        $this->security = $security;        

        $this->discussionParticipantRepository = $discussionParticipantRepository;
        $this->messageParticipantRepository = $messageParticipantRepository;

    }

    #[Route('/read/message/discussion/{uuid}', name: 'app_read_message', methods: ['GET'])]
    public function readMessage($uuid)
    {

        $user = $this->security->getUser();
    
        $discussionByUid = $this->discussionRepository->findOneBy(['uuid' => $uuid]);
        $messagesByDiscussion = $this->messageRepository->findBy(['discussion' => $discussionByUid ], ['createdAt' => 'DESC'], 100, 0 );

        $discussionStatusParticipant = $this->discussionParticipantRepository->getStatutByDiscussionAndParticipant($discussionByUid,$user);
        
        $discussionStatusParticipant->setStatus(DiscussionParticipant::READ);


       
        foreach ($messagesByDiscussion as $message) {
            $messageStatusParticipant = $this->messageParticipantRepository->getStatutByMessageAndParticipant($message, $user);
            
            $messageStatusParticipant->setStatus(MessageParticipant::READ);
            $this->entityManager->persist($messageStatusParticipant);
        }
        $this->entityManager->flush();

        if(!$user || !$discussionByUid->isParticipantInDiscussion($user)) throw new AccessDeniedException('message');
        $arrayOfMessage = [];
        foreach ($messagesByDiscussion as $message) {
            // $message->getMessageParticipants()
            $arrayOfMessage [] = $message->toArray(); 
        }
        return $this->json($arrayOfMessage);
    }
    
    
}
