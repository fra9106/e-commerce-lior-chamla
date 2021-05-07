<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use App\Services\Purchase\Persister;
use App\Services\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    protected $manager;
    protected $cartService;
    protected $persister;

    public function __construct(EntityManagerInterface $manager, CartService $cartService, Persister $persister)
    {
        $this->manager = $manager;
        $this->cartService = $cartService;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirmation", name="app_purchase_confirmation")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour soumettre ce formulaire")
     */
    public function confirmation(Request $request): Response
    {

        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() && $form->isValid()) {

            $this->addFlash('message', 'Vous devez remplir le formulaire 😞 !');
            return $this->redirectToRoute('app_cart_show');
        }

        $cart = $this->cartService->getDetailCartItems();
        if (count($cart) === 0) {
            $this->addFlash('message', 'Vous ne pouvez pas enregistrer une commande avec un panier vide 😞 !');
            return $this->redirectToRoute('app_cart_show');
        }

        $purchase = new Purchase();

        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->persistConfirmPurchase($purchase);

        //$this->cartService->empty($purchase);

        //$this->addFlash('message', 'commande enregistré !');
        return $this->redirectToRoute('app_purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}
