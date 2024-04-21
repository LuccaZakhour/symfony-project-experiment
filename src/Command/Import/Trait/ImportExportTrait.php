<?php

namespace App\Command\Import\Trait;

trait ImportExportTrait {

    public function getBaseDir($baseFilePath, $exportClientId)
    {
        return $baseFilePath . '/clientId/' . $exportClientId;
    }

    public function getExperimentSectionDir($baseFilePath, $exportClientId, $journalID)
    {
        return $baseFilePath . '/clientId/' . $exportClientId . '/experiment_sections/' . $journalID . '/files';
    }
}