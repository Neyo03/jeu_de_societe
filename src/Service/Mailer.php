<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer { 

    private $twig;
    private $mailer;
    private $parameterBagInterface;
    public function __construct(Environment $twig, MailerInterface $mailer, ParameterBagInterface $parameterBagInterface )
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
         $this->parameterBagInterface = $parameterBagInterface;
    }
    /**
     * send email to user
     *
     * @param $user
     * @param $subject
     * @param $template
     * @param array $datas
     * @return void
     */
    public function send($user,$subject, $template, $datas = []){

        $from = $this->parameterBagInterface->get('mailer_from');
        $from_name = $this->parameterBagInterface->get('mailer_from_name');

        $message = (new Email())
            ->from(new Address($from, $from_name))
            ->subject($subject)
            ->to($user->getEmail())
            ->html(
                $this->twig->render($template, $datas)
            )
        ;
        $this->mailer->send($message);
    }
}