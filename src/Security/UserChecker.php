<?php

namespace App\Security;

use App\Entity\User as AppUser;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function checkPreAuth(UserInterface $user): void
    {
      
        if (!$user instanceof AppUser) {
            return;
        }

        if (new DateTime() < $user->getBlockedExpirationAt()) {
            
            throw new CustomUserMessageAccountStatusException('Your user account is actually blocked, please retry in a few moments');
        }
        // if ($user->getFailedConnectionCount()<5) {
        //     throw new CustomUserMessageAccountStatusException("Connection failed you have ". 5-$user->getFailedConnectionCount()." attempts left");
        // }
        if ($user->getFailedConnectionCount()>= 5) {
            throw new CustomUserMessageAccountStatusException('You have too many failed login attempts, your account is blocked for 30 minutes');
        }
       
        
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

       
    }
}