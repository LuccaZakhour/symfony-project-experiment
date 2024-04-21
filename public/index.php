<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

require '../vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

$request = Request::createFromGlobals();

$clientId = false;

$isAdmin = pathinfo(parse_url($_SERVER['REQUEST_URI'])['path'])['filename'] === 'admin';
$isLogin = pathinfo(parse_url($_SERVER['REQUEST_URI'])['path'])['filename'] === 'login';
$isRoot = pathinfo(parse_url($_SERVER['REQUEST_URI'])['path'])['dirname'];

if (!array_key_exists('DATABASE_URL', $_ENV)) {

    $clientId = array_key_exists('HTTP_X_CLIENT', $_SERVER) ? $_SERVER['HTTP_X_CLIENT'] : $_GET['c'] ?? null;

    if (!$clientId) {
        $clientId = $request->cookies->get('client_id');
    }

    if (!$clientId) {
        $pattern = "/\/web\/(\d+)\//"; // match any sequence of digits surrounded by forward slashes
        if (preg_match($pattern, $_SERVER['REQUEST_URI'], $matches)) {
            $clientId = $matches[1];
        };
    }

    if ($clientId) {
        $clients = (require '../config/getClients.php');

        if (false === $clients) {
            header('HTTP/1.1 500 Internal Server Error');
            exit('clients file not configured');
        }


        if (!array_key_exists($clientId, $clients)) {
            error_log("Redirecting due to condition X");

            $response = new RedirectResponse($_ENV['API_ENDPOINT'] . '/client.php');
            // exit if statement
            $response->send();
            /*
            header('HTTP/1.1 400 Bad Request');
            exit('Unknown client');
            */
        }

        $clientData = $clients[$clientId];

        $_ENV['CLIENT_ID'] = $clientId;
        putenv('CLIENT_ID=' . $_ENV['CLIENT_ID']);

        $passwordEncoded = urlencode($clientData['password']);

        $_ENV['DATABASE_URL'] = "mysql://{$clientData['user']}:{$passwordEncoded}@{$clientData['host']}:3306/{$clientData['database']}";
        putenv('DATABASE_URL=' . $_ENV['DATABASE_URL']);
    } else {
        putenv('DATABASE_URL=mysql://x:x@localhost/x');

        if (!$clientId && ($isLogin || $isRoot)) {
            error_log("Redirecting due to condition X #2");
            
            $response = new RedirectResponse($_ENV['API_ENDPOINT'] . '/client.php');
            $response->send();
        }
    }
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);

if (isset($clientId)) {
    // Store the client ID as a request attribute.
    $request->attributes->set('initial_client_id', $clientId);
}

$response = $kernel->handle($request);

if (isset($clientId)) {
    $cookieExists = $request->cookies->get('client_id');
    $cookieExists || $response->headers->setCookie(Cookie::create('client_id', $clientId));
}

$response->send();
$kernel->terminate($request, $response);


