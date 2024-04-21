<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ClientIdSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        // Fetch the initial client ID set in public/index.php
        $clientId = $request->attributes->get('initial_client_id', null);
        $clientId || $clientId = $request->cookies->get('client_id');

        if ($clientId !== null) {

            $this->setEnvFromClientId($clientId);
        }
    }

    public function setEnvFromClientId($clientId)
    {
        $clients = (require '../config/getClients.php');
        $clientData = $clients[$clientId];

        $_ENV['CLIENT_ID'] = $clientId;
        putenv('CLIENT_ID=' . $_ENV['CLIENT_ID']);

        $passwordEncoded = urlencode($clientData['password']);

        $_ENV['DATABASE_URL'] = "mysql://{$clientData['user']}:{$passwordEncoded}@{$clientData['host']}:3306/{$clientData['database']}";
        putenv('DATABASE_URL=' . $_ENV['DATABASE_URL']);
    }
}
