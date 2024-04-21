<?php

namespace App\Service\Trait;

trait ReplaceVariablesTrait
{
    

    public function replaceVariables($content, $variableRepository)
    {
        preg_match_all('/\{\{\s*var\(\'id:(\d+)\'\)\s*\}\}/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $id = $match[1]; // Extracted ID
            $variable = $variableRepository->find($id);
            if ($variable) {
                $value = $variable->getContents();
                // Define the replacement HTML with inline style and tooltip
                $replacementHtml = "<span style='background-color: #e9ecef; color: #000; padding: 2px 4px; border-radius: 4px;' data-bs-toggle='tooltip' data-bs-placement='top' title='ID: {$id}'>{$value}</span>";
            } else {
                // Fallback in case variable is not found
                $replacementHtml = "<span style='background-color: #f8d7da; color: #721c24; padding: 2px 4px; border-radius: 4px;'>Value not found</span>";
            }

            // Replace the custom syntax with its corresponding value and HTML
            $content = str_replace($match[0], $replacementHtml, $content);
        }

        return $content;
    }
}
