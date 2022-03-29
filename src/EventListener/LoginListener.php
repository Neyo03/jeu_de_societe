<?php

namespace App\EventListener;
 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginListener extends AbstractController
{
    private $em;
    private $request;
 
    public function __construct(EntityManagerInterface $em, RequestStack $request)
    {
        $this->em = $em;
        $this->request = $request;
    }
 
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the User entity.
       /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLoginAt(new \DateTime());

        $session = $this->request->getSession();
        if ($session instanceof Session) {
            $this->addFlash('success', "Your are successfully logged in as ".$user->getEmail());
            
            if ($user->getIsEnabled() === false) {
                $this->addFlash('info', "Please valid your account in ".$user->getEmail());
            }
            $this->em->persist($user);
            $this->em->flush();
        }
    
        
        
        
      
    }
}