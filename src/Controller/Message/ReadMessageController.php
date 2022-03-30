<?php

namespace App\Controller\Message;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use App\Form\CreateDiscussionFormType;
use App\Form\MessageType;
use App\Repository\DiscussionParticipantRepository;
use App\Repository\DiscussionRepository;
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

    public function __construct(EntityManagerInterface $entityManager, MessageRepository $messageRepository, DiscussionRepository $discussionRepository, Security $security)
    {
        $this->entityManager = $entityManager;       
        $this->messageRepository = $messageRepository;       
        $this->discussionRepository = $discussionRepository;  
        $this->security = $security;        

    }

    #[Route('/read/message/discussion/{uuid}', name: 'app_read_message', methods: ['GET'])]
    public function readMessage($uuid)
    {
       
        $user = $this->security->getUser();
    
        $discussionByUid = $this->discussionRepository->findOneBy(['uuid' => $uuid]);
        $messagesByDiscussion = $this->messageRepository->findBy(['discussion' => $discussionByUid ]);

        if(!$user || !$discussionByUid->isParticipantInDiscussion($user)) throw new AccessDeniedException('message');
        $arrayOfMessage = [];
        foreach ($messagesByDiscussion as $message) {
            $arrayOfMessage [] = $message->toArray(); 
        }
        return $this->json($arrayOfMessage);
    }
    
    
}
