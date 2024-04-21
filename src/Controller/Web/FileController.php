<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController extends AbstractController
{
    /**
     * @Route("/files/{path}", name="file_serve", requirements={"path"=".*"}, methods={"GET"})
     */
    public function index(string $path, Request $request): Response
    {


        $reversedPath = strrev($path);
        // Replace the first occurrence of reversed target with reversed replacement in the reversed path
        $replacedReversedPath = preg_replace('/\.(\/)/', '.', $reversedPath, 1);
        // Reverse the path back to original order, now with the last occurrence replaced
        $finalPath = strrev($replacedReversedPath);

        $finalPath = str_replace('_.', '.', $path);

        // get absoulte path
        $finalPath = realpath('./../../files/' . $finalPath);

        // Ensure the file exists and is readable
        if (!file_exists($finalPath) || !is_readable($finalPath) || !str_contains($finalPath, 'files/clientId')) {
            throw $this->createNotFoundException('File does not exist.');
        }

        //$finalPath = str_replace('_.', '.', $path);

        $response = new BinaryFileResponse($finalPath);

        $response->headers->set('Content-Type', 'application/pdf');

        // Or to display inline, particularly useful for images or PDFs
        $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        basename($finalPath)
    );
    
        return $response;

        //return new Response('File content or error message here');
    }
}