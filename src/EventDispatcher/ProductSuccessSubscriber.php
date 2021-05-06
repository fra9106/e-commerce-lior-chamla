<?php 

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSuccessSubscriber implements EventSubscriberInterface
{

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendViewProduct'
        ];
    }

    public function sendViewProduct(ProductViewEvent $productViewEvent)
    {
        //dd($productViewEvent);
        $this->logger->info('Un mail vous a été envoyé Mr l\'admin !' .
        $productViewEvent->getProduct()->getId());
    }
}