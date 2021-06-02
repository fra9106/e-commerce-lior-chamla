<?php

namespace App\Controller\Purchase;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases/list", name="app_purchases_list")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function purchasesList(): Response
    {
        /** @var User */
        $user = $this->getUser();
        return $this->render('purchases_list/purchases_list.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}
