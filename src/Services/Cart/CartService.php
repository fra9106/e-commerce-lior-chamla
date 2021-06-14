<?php

namespace App\Services\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    protected $session;
    protected $productRepository;

    public function __construct(sessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function add(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
        $cart[$id]++;

        $this->saveCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        $cart = $this->getCart();

        foreach ($cart as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += ($product->getPrice() * $qty);
        }

        return $total;
    }

    /**
     * Undocumented function
     *
     * @return array<CartItem>
     */
    public function getDetailCartItems(): array
    {
        $detailedCart = []; // à la base c'est un tableau qui est vide

        $cart = $this->getCart();

        foreach ( $cart as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if( $cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
    }

}
