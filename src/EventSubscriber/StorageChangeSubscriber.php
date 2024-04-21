<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::postUpdate, method: 'onPostUpdate')]
class StorageChangeSubscriber implements EventSubscriber
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
            Events::postPersist,
            Events::postRemove,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->invalidateCache($args);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->invalidateCache($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->invalidateCache($args);
    }

    private function invalidateCache(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // Check if the instance is of the Storage entity
        if (!$entity instanceof \App\Entity\Storage) {
            return;
        }

        // Invalidate the cache
        $cacheTitle = 'client_id';
        $this->cache->delete($cacheTitle);
    }
}
