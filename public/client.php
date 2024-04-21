<?php

require '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

$error = false;

$clientId = $_GET['client_id'] ?? null;
if (null !== $clientId) {
    $clients = (require '../config/getClients.php');

    if (false === $clients) {
        header('HTTP/1.1 500 Internal Server Error');
        exit('clients file not configured');
    }

    if (!array_key_exists($clientId, $clients)) {
        header('HTTP/1.1 400 Bad Request');
        $error = true;
    } else {
        $response = new RedirectResponse('/login');
        $response->headers->setCookie(Cookie::create('client_id', $clientId));

        return $response->send();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LabOwl</title>

    <link href="./lib/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container client-wrapper mt-4 align-items-center d-flex flex-column">
    <div class="row">
        <div class="col-12">
            <div class="logo text-center">
                <img src="img/LabOwlLogo.png" alt="LabOwl Logo"/>
            </div>

            <div class="col-12 card">
                <?php if ($error): ?>
                    <div class="alert alert-danger" id="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                        </svg>
                        Ungültige Client-ID angegeben
                    </div>
                <?php endif; ?>

                <form method="get">
                    <div class="mb-3">
                        <label for="clientId" class="form-label">Kundennummer</label>
                        <input name="client_id" type="text" class="form-control" id="clientId">
                    </div>
                    <button type="submit" class="btn btn-primary d-flex m-auto">Weiter</button>
                </form>
            </div>
        </div>
    </div>
</body>

<style>
    body {
        background-color: #f1f5f9;
    }

    .client-wrapper {
        margin: 0 auto;
        max-width: 28rem;
    }

    .logo {
        margin-top: 120px;
        margin-bottom: 20px;
    }

    .logo img {
        width: 40%;
        height: auto;
    }

    .card {
        box-radius: 4px;
        padding: 2rem 2.5rem;
        border-color: white;
        box-shadow: 0 10px 15px -3px rgba(15, 23, 43, .1), 0 4px 6px -4px rgba(15, 23, 42, .1);
    }

    label {
        font-size: 14px;
        font-weight: 500;
    }

    .btn {
        background-color: #5368d5;
        border: 0;
    }
</style>
