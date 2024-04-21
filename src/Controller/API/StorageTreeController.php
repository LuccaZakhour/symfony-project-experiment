<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Storage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class StorageTreeController extends AbstractController
{
    
    public function __construct(private UrlGeneratorInterface $urlGenerator, private CacheInterface $cache)
    {
    }
    
    #[Route('/api/storage/tree', name: 'api_storage_tree', methods: ['GET'])]
    public function getStorageTree(EntityManagerInterface $entityManager): JsonResponse
    {
        $cacheTitle = 'storage_tree' . '_clientId-' . $_ENV['CLIENT_ID'];
        $tree = $this->cache->get($cacheTitle, function (ItemInterface $item) use ($entityManager) {
            $item->expiresAfter(36000); // Cache for 1 hour, adjust as needed
    
            $storages = $entityManager->getRepository(Storage::class)->findBy(['parent' => null]);
            return $this->buildTree($storages);
        });
    
        return $this->json($tree);
    }
    

    private function buildTree($storages)
    {
        $tree = [];
        foreach ($storages as $storage) {
            $tree[] = $this->formatForTree($storage);
        }
        return $tree;
    }

    private function formatForTree($storage)
    {
        $children = [];
        foreach ($storage->getChildren() as $child) {
            $children[] = $this->formatForTree($child);
        }

        $detailUrl = $this->urlGenerator->generate('admin', [
            'crudAction' => 'detail',
            'crudControllerFqcn' => 'App\Controller\Admin\StorageCrudController',
            'entityId' => $storage->getId(),
        ]);

        return [
            'id' => $storage->getId(),
            'text' => $storage->getName(), // jsTree uses 'text' for node names
            'children' => $children,
            'a_attr' => ['href' => $detailUrl, 'class' => 'custom-class'],
            //'icon' => '//jstree.com/tree.png',
        ];
    }
}
