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
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

       
    }
}