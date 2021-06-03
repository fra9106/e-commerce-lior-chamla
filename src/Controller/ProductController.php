<?php

namespace App\Controller;

use App\Event\ProductViewEvent;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/categorie/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $repo): Response
    {
        $category = $repo->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("Cette catégorie n'existe pas !");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}/", name="show_product", priority=-2)
     */
    public function show($slug, $prenom, ProductRepository $productRepository, Request $request, EventDispatcherInterface $dispatcher )
    {       
        //dd($request->attributes);

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Ce produit n'existe pas !");
        }

        //$productViewEvent = new ProductViewEvent($product);
        //$dispatcher->dispatch($productViewEvent, 'product.view');

        return $this->render('product/productShow.html.twig', [
            'slug' => $slug,
            'product' => $product
        ]);
    }
}
