<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CartConfirmationType;
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
            throw $this->createNotFoundException("Le produit $id n'existe pas ! 😞");
        }

        $this->service->add($id);
        $this->addFlash('message', 'Produit rajouté dans votre panier ! 🤩');

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("app_cart_show");
        }

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
    public function showCart(CartService $service, Request $request) : Response
    {
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        $detailedCart = $service->getDetailCartItems();

        $total = $service->getTotal();

        return $this->render('cart/cart.html.twig', [
            'items' => $detailedCart,
            'total' => $total,

            'form' => $form->createView(),

        ]);
    }

    /**
     * Undocumented function
     *
     * @Route("/cart/delete/{id}", name="app_cart_delete", requirements={"id":"\d+"})
     */
    public function delete($id, ProductRepository $productRepository, CartService $service)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Impossible de supprimer le produit $id, car ce produit n'existe pas ! 😞");
        }

        $service->remove($id);

        $this->addFlash('message', 'Le produit a bien été supprimé de votre panier ! 🤩');

        return $this->redirectToRoute('app_cart_show');
    }

    /**
     * @Route("/cart/decrement/{id}", name="app_cart_decrement", requirements={"id": "\d+"})
     */
    public function decrement($id, ProductRepository $productRepository, CartService $service)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Vous ne pouvez pas supprimer une quantité de ce produit, car le produit $id n'existe pas ! 😞");
        }

        $service->decrement($id);

        $this->addFlash("message", "La quantité de ce produit à bien été mise à jour 🤩");

        return $this->redirectToRoute("app_cart_show");
    }
}
