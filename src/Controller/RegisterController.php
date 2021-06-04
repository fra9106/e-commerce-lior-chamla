<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request, 
        UserPasswordEncoderInterface $encoder, 
        EntityManagerInterface $entityManager, 
        GuardAuthenticatorHandler $guardHandler, 
        LoginFormAuthenticator $authenticator, 
        MailerInterface $mailer
        ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $avatar = $form->get('picture')->getData();
            if($avatar){
                $fichier = md5(uniqid()) . '.' . $avatar->guessExtension();
                
                $avatar->move(
                    $this->getParameter('img_profile_directory'),
                    $fichier
                );

                $user->setPicture($fichier);
            }
            // On génère un token et on l'enregistre
            $user->setActivationToken(md5(uniqid()));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $message = (new TemplatedEmail())
                ->from('noreply@monpersoweb.fr')
                ->to($user->getEmail())
                ->htmlTemplate('mail/activation.html.twig')
                ->context([
                    'user' => $user->getFirstName(),
                    'token' => $user->getActivationToken(),
                    'expiration_date' => new \DateTime('+1 hour')
                ]);
            // we send mail
            $mailer->send($message);

            //addFlash confim mess
            $this->addFlash('message', 'Un mail vous a été envoyé merci de confirmer votre compte !');
            return $this->redirectToRoute('app_login');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="app_activation", priority=-1)
     */
    public function activation(
        $token, 
        EntityManagerInterface $entityManager, 
        UserRepository $user)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $newUser = $user->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if (!$newUser) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $newUser->setActivationToken(null);

        $entityManager->persist($newUser);
        $entityManager->flush();

        // On génère un message
        $this->addFlash('message', 'Votre compte a été activé avec succès 🤩 !');

        // On retourne à l'accueil
        return $this->redirectToRoute('app_homepage');
    }
}
