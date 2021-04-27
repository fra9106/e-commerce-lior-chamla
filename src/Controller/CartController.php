<?php

namespace App\Controller;

use App\Entity\Product;
use App\Services\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    protected $service;

    public function __construct(CartService $service)
    {
        $this->service = $service;
    }
    /**
     * @Route("/cart/add/{id}", name="app_cart_add", requirements={"id":"\d+"})
     */
    public function add($id, Request $request, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        $this->service->add($id);
        $this->addFlash('message', 'Produit rajouté dans votre panier !');

        /*if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("show_product");
        }*/

        return $this->redirectToRoute('show_product', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * show cart
     * 
     *@Route("/cart", name="app_cart_show")
     */
    public function showCart(CartService $service)
    {
        $detailedCart = $service->getDetailCartItems();

        $total = $service->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total

        ]);
    }
}
