<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Form\ProfileUserFormType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile-user", name="app_profile_user")
     * 
     * @return Response
     */
    public function ProfileUser(): Response
    {
        return $this->render('user/profile_user.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/edit-profile-user", name="app_edit_profile_user")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function editProfileUser(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $avatar = $form->get('picture')->getData();
            if($avatar){
                $file = md5(uniqid()) . '.' . $avatar->guessExtension();
                
                $avatar->move(
                    $this->getParameter('img_profile_directory'),
                    $file
                );
                $user->setPicture($file);
            }
                $manager->flush();
                $this->addFlash('message', 'Profil modifié 😁 !');

                return $this->redirectToRoute(
                    'app_profile_user'
                );

        }

        return $this->render('user/edit_profile_user.html.twig', [
            'profileUserForm' => $form->createView()
        ]);

    }

    /**
     * @Route("/password-update", name="app_password_update")
     * 
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function passwordUpdate(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response {

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                $form->get('oldPassword')->addError(new FormError("Mauvais mot de passe ! 😕"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $encoded = $passwordEncoder->encodePassword($user, $newPassword);

                $user->setPassword($encoded);
                $manager->persist($user);
                $manager->flush();


                $this->addFlash('message', "Votre nouveau mot de passe à bien été pris en compte 😊 ");

                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('security/new_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
