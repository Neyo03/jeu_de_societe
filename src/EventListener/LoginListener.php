<?php

namespace App\EventListener;
 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LoginListener extends AbstractController
{
    private $em;
 
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
 
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the User entity.
       /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLoginAt(new \DateTime());
        $user->setFailedConnectionCount(0);
        
        $this->addFlash('success', "Your are successfully logged in as ".$user->getEmail());

        if ($user->getIsEnabled()===false) {
            $this->addFlash('info', "Please valid your account in ".$user->getEmail());
        }
        
        $this->em->persist($user);
        $this->em->flush();
      
    }
}