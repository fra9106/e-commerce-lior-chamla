<?php

namespace App\Services\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Services\Cart\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Persister
{

    protected $security;
    protected $manager;
    protected $cartService;

    public function __construct(Security $security, EntityManagerInterface $manager, CartService $cartService)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->cartService = $cartService;
    }
    public function persistConfirmPurchase(Purchase $purchase)
    {

        $purchase->setUser($this->security->getUser())
                 ->setTotal($this->cartService->getTotal())
                 ->setPurchaseAt(new DateTime());



        $this->manager->persist($purchase);

        foreach ($this->cartService->getDetailCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setProduct($cartItem->product)
                ->setPurchase($purchase)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());
                //dd($purchaseItem);

            $this->manager->persist($purchaseItem);
        }

        $this->manager->flush();
    }
}
