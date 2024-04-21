<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';


define('DIR', dirname(__DIR__));

(new Dotenv())->loadEnv(DIR . '/.env');
if (!isset($_GET['apiKey']) || $_GET['apiKey'] != $_ENV['APP_ADMIN_KEY']) {
    die('invalid key');
}

$clientsFilePath = $_ENV['CLIENTS_PATH'];

if (!file_exists($clientsFilePath)) {
    touch($clientsFilePath);

    file_put_contents($clientsFilePath, serialize([]));
}

$clients = unserialize(file_get_contents($clientsFilePath));

$action = $_GET['action'] ?? 'none';
$client = null;
if (isset($_GET['client'])) {
    if (!isset($clients[$_GET['client']])) {
        die('unknown client');
    }

    $client = $clients[$_GET['client']];

    putenv("DATABASE_URL=mysql://{$client['user']}:{$client['password']}@{$client['host']}:3306/{$client['database']}");
}

$clientLessActions = ['instance_add'];
if (null === $client && !in_array($action, $clientLessActions)) {
    die('unknown client');
}

switch ($action) {
    case 'instance_add':

        $clients[$_GET['client_id']] = [
            'host' => $_GET['host'],
            'database' => $_GET['database'],
            'user' => $_GET['user'],
            'password' => $_GET['password'],
        ];


        if (!file_exists(dirname($clientsFilePath))) {
            mkdir(dirname($clientsFilePath), 0777, true); // Adjust permissions as needed
        }

        $resPutContents = file_put_contents($clientsFilePath, serialize($clients));

        if ($resPutContents === false) {
            $error = error_get_last();
        }

        $client = $clients[$_GET['client_id']];

        $uploadDir = $_ENV['APP_FILES_PATH'] . '/' . $_GET['client_id'] . '/';
        $publicDir = '/_files/' . $_GET['client_id'] . '/';
        $tmpDir = $_ENV['APP_FILES_PATH'] . '/_tmp/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0660, true);
        }
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir, 0660, 
            
            true);
        }

        if (!file_exists($clientsFilePath)) {
            // Attempt to touch the file and check for success
            if (!touch($clientsFilePath)) {
                // If touch() fails, output the last error
                $error = error_get_last();
                echo "Error: " . $error['message'];
            }
        }

        putenv("DATABASE_URL=mysql://{$client['user']}:{$client['password']}@{$client['host']}:3306/{$client['database']}");
        putenv("CLIENT_ID=" . $_GET['client_id']);

        //echo 'php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:database:remove' . PHP_EOL;
        $resDbRemove = system('php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:database:remove');

        //echo 'php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:database:create ' . $_GET['client_id'] . PHP_EOL; # TODO: add CREATE TABLEs to v1.php migration
        $dbCreateCmd = 'php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:database:create ' . $_GET['client_id'];
        $resDeploymentDbCreate = system($dbCreateCmd);

        // json parse from string resDeploymentDbCreate
        $resDeploymentDbCreateArr = json_decode($resDeploymentDbCreate, true);

        $demo_end = isset($_GET['demo_end']) ? $_GET['demo_end'] : '';
        
        system('php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:instance:create ' . $_GET['client_id']
          . ' ' . $_GET['admin_email'] . ' ' . $_GET['password'] . ' ' . $resDeploymentDbCreateArr['name'] . ' ' . $resDeploymentDbCreateArr['password']
           . ' ' . $resDeploymentDbCreateArr['host'] . ' ' . $demo_end . '  > /dev/null 2>&1');

        //echo 'php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' doctrine:migration:migrate -n:' . PHP_EOL;
        $resDoctrineMigration = system('php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' doctrine:migration:migrate -n  > /dev/null 2>&1');

        return $resDeploymentDbCreate;

        break;

    case 'instance_remove':

        $client = $clients[$_GET['client_id']];

        putenv("DATABASE_URL=mysql://{$client['user']}:{$client['password']}@{$client['host']}:3306/{$client['database']}");
        putenv("CLIENT_ID=" . $_GET['client_id']);

        echo 'php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:database:remove ' . $_GET['client_id'] . PHP_EOL;
        system('php "' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:database:remove ' . $_GET['client_id']);

        echo 'php ' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:instance:remove ' . $_GET['client_id'] . PHP_EOL;
        system('php ' . DIR . '/bin/consoleForClient" ' . $_GET['client_id'] . ' deployment:instance:remove ' . $_GET['client_id']);
        
        unset($clients[$_GET['client']]);

        file_put_contents($clientsFilePath, serialize($clients));

        break;

    case 'backup_create':

        
        $client = $clients[$_GET['client']];

        putenv("DATABASE_URL=mysql://{$client['user']}:{$client['password']}@{$client['host']}:3306/{$client['database']}");
        putenv("CLIENT_ID=" . $_GET['client_id']);
        // TODO: focus here
        $execStr = 'php ' . DIR . '/bin/consoleForClient ' . $_GET['client'] . ' backup:create-backup ' . $client['host'] . 
            ' ' . $client['database'] . ' ' . $client['user'] . ' ' . $client['password'] .
            ' ' . $_GET['path_to_dir'] . ';';

        echo $execStr . PHP_EOL;
        //echo json_encode($client) . PHP_EOL;
        system($execStr);

        break;
    case 'backup_restore':
        // TODO: focus here
        $restorePath = $_GET['restore_path'];

        $client = $clients[$_GET['client']];

        $execStr = sprintf('mysql -u %s --password=%s -h %s %s<%s',
            $client['user'],
            $client['password'],
            $client['host'],
            $client['database'],
            $restorePath,
        );

        echo $execStr . PHP_EOL;
        system($execStr);
        break;
    case 'default':
        echo 'unknown action';
        break;
}