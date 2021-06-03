<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
