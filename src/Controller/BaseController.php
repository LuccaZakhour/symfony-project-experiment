<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Response\ApiSuccessResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseController extends AbstractController
{

    public function serialize($object, array $groups, $limit = 0)
    {
        $normalizer = new ObjectNormalizer(new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())), null, null, null, null, null, [
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            //  ObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => $limit
        ]);

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);

        return $serializer->normalize($object, null, ['groups' => $groups]);
    }
}