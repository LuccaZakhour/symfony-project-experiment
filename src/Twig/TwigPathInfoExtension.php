<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

// 4.4.2024. I can define export and import constants here without having to guess correct paths
// This trait is unused at the moment
class TwigPathInfoExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_file_extension', [$this, 'getFileExtension']),
            new TwigFunction('get_public_file_path', [$this, 'getPublicFilePath']),
            new TwigFunction('get_public_file_path_without_extension', [$this, 'getPublicFilePathWithoutExtension']),
        ];
    }

    public function getFileExtension(?string $path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
        if (empty($path)) {
            return null;
        }
    }

    public function getPublicFilePath(?string $path)
    {
        // Break the path into its components
        $path = pathinfo($path);

        // Rebuild the path with the extension dot replaced by an underscore
        $path = $path['dirname'] . '/' . $path['filename'] . '_.' . $path['extension'];

        # $path eg. /clientId/1/experiment_sections/4102448/files/2588006_20190524-AH_21_AB-Bradford-EPZ_and_Insulin.jpg

        $apiEndpoint = $_ENV['API_ENDPOINT'];

        $fullPublicFilePath = $apiEndpoint . '/files' . $path;

        return $fullPublicFilePath;
    }

    public function getPublicFilePathWithoutExtension(?string $path)
    {
        # $path eg. /clientId/1/experiment_sections/4102448/files/2588006_20190524-AH_21_AB-Bradford-EPZ_and_Insulin.jpg

        $apiEndpoint = $_ENV['API_ENDPOINT'];

        $fullPublicFilePath = $apiEndpoint . '/files' . pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);

        return $fullPublicFilePath;
    }
}