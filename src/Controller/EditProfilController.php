<?php

namespace App\Controller;

use App\Form\EditUserFormType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EditProfilController extends AbstractController
{
    #[Route('/edit/profil/{uuid}', name: 'app_edit_profil')]
    public function edit($uuid, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {

        $user = $userRepository->findOneBy(['uuid' =>$uuid]);

        if ($user !== $security->getUser()) {
            return $this->redirectToRoute("app_home");  
        }

        $form = $this->createForm(EditUserFormType::class, $user, [
            'validation_groups' => ['default']
        ]);
        $form->handleRequest($request);

        // dd($form->getData());
        if ($form->isSubmitted() &&  $form->isValid()) {

            $profil_picture_user = $form->get("profilPicture")->getData();
            
            if ($profil_picture_user) {
                $nomOriginalProfilPicture = pathinfo($profil_picture_user->getClientOriginalName(), PATHINFO_FILENAME);

                $nomUniqueProfilPicture = $nomOriginalProfilPicture . "-" . time() . "." . $profil_picture_user->guessExtension();
                $profil_picture_user->move('uploads/profil_picture/'.$user->getId(), $nomUniqueProfilPicture);
                $user->setProfilPicture($nomUniqueProfilPicture);
            }

            $user->setUpdatedAt(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();  
        }

        return $this->render('edit_profil/index.html.twig', [
            'form'=> $form->createView(),
            'user' => $user,
        ]);
    }
}
