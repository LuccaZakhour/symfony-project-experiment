<?php

use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__ . '/../.env');
}

$clients = (require __DIR__ . '/../config/getClients.php');
$dir = realpath(__DIR__ . '/..');

foreach ($clients as $client) {
    try {
        putenv("DATABASE_URL=mysql://{$client['user']}:{$client['password']}@{$client['host']}:3306/{$client['database']}");
        putenv("APP_ENV=dev");

        $commandStr = $_ENV['PHP_EXEC'] . ' "' . $dir . '/bin/console" cache:clear';
        $clientId = substr($client['database'], -1);

        $_SERVER['HTTP_X_CLIENT'] = $clientId;

        exec($commandStr);

        echo 'OK' . PHP_EOL;
    } catch (\Exception $e) {
        echo 'ERROR' . PHP_EOL;
    }
}


