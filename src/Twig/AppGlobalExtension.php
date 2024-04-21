<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppGlobalExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'API_ENDPOINT' => $_ENV['API_ENDPOINT'] ?? 'default_value',
        ];
    }
}
