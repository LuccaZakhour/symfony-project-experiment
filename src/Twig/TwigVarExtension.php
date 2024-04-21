<?php

namespace App\Twig;

use App\Entity\Variable;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

// {{ var('id:51') }}
class TwigVarExtension extends AbstractExtension
{
    private $entityManager;
    private $twig;

    public function __construct(EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('var', [$this, 'getVar']),
        ];
    }
    
    public function getVar($expression)
    {
        [$key, $value] = explode(':', $expression, 2);
    
        if ($key === 'id') {
            $repository = $this->entityManager->getRepository(Variable::class);
            $variable = $repository->find($value);
    
            if ($variable) {
                // Attempt to render the variable content as a Twig template
                $contents = $variable->getContents();
                return $this->twig->createTemplate($contents)->render([]);
                // Note: You can pass variables to the render method if needed
            }
        }
    
        return null;
    }
}
