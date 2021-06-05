<?php 

namespace App\Controller;

use App\Form\SearchProductType;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
    * @Route("/", name="app_homepage")
    *
    * @return void
    */
    public function homepage(ProductRepository $productRepository, Request $request)
    {
        $products = $productRepository->findBy([], [], 3);

        $form = $this->createForm(SearchProductType::class);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $products = $productRepository->search($search->get('words')
            ->getData());
        }

        
        return $this->render('Home/home.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }
}