<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function protocols($makeRequest, $db, $client) {
    foreach ($makeRequest('protocols') as $row) {
        $exportData['protocols'][$row['protID']] = $row;
    }
    insertIntoTable($db, 'protocols', $exportData['protocols'], 'protID');
        
    $protocols = $db->query('SELECT * FROM protocols')->fetchAll(PDO::FETCH_ASSOC);

    # for each $protocols make request to /api/v1/protocols/{protID}
    # and scrape protocol_steps from appViewURL parameter from body
    foreach ($protocols as $protocol) {
        $protocol_id = $protocol['protID'];

        $responseRaw = $client->post('https://www.elabjournal.com/protocols/handlers/getProtocol.ashx', [
            'form_params' => [
                'protVersionID' => $protocol['protVersionID'],
            ]
        ]);

        // get contents from stream
        $protocolData = json_decode($responseRaw->getBody()->getContents(), true);

        $protocolVars = $protocolData['prot']['vars'];
        $protocolSteps = $protocolData['prot']['steps'];
        foreach ($protocolSteps as &$protocolStep) {
            $protocolStep['protID'] = $protocol['protID'];
            $protocolStep['protVersionID'] = $protocol['protVersionID'];
        }

        foreach ($protocolVars as &$protocolVar) {
            $protocolVar['protID'] = $protocol['protID'];
            $protocolVar['protVersionID'] = $protocol['protVersionID'];
        }
        
        insertIntoTable($db, 'protocol_steps', $protocolSteps, 'stepID');
        insertIntoTable($db, 'protocol_variables', $protocolVars, 'varID');
    }
}

protocols($makeRequest, $db, $client);
