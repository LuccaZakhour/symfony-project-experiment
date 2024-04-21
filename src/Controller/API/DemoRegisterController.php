<?php
namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DemoRegisterController extends AbstractController
{
    /**
     * @Route("/demoregistration/register", name="demo_registration", methods={"POST"})
     */
    #[Route('/api/demo_register/register', name: 'api_demo_register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        var_dump("test");
        return true;
    }
}
?>