<?php

namespace App\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploadController extends AbstractController
{
    /**
     * To be used by CK-editor
     * @Route("/api/image/upload", name="image_upload", methods={"POST"})
     */
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('upload'); // 'upload' is the name field in CKEditor upload form
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // You should sanitize the filename and ensure uniqueness
            $safeFilename = $originalFilename; // Implement sanitization and uniqueness
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            
            try {
                // Move the file to the directory where images are stored
                // Update "targetDirectory" with your desired path
                $file->move(
                    $this->getParameter('targetDirectory'),
                    $newFilename
                );

                // Return a JSON response with the URL to the uploaded file
                // Update "/uploads/images/" to the path where the image is accessible
                return new JsonResponse(['url' => '/uploads/images/'.$newFilename]);
            } catch (FileException $e) {
                // Handle exception if something happens during file upload
            }
        }

        return new JsonResponse(['error' => 'No file uploaded'], 400);
    }
}

