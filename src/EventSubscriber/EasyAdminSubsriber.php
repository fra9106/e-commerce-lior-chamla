<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class EasyAdminSubcriber implements EventSubscriberInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setProductSlugg'],
        ];
    }

    public function setProductSlugg(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof Product)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getName());
        $entity->getSlug($slug);
    }
}
