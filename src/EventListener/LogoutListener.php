<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use Psr\Log\LoggerInterface;

class LogoutListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onLogout(LogoutEvent $event)
    {
        // TEST : Écriture dans un fichier ailleurs (éviter permissions root)
        file_put_contents('/tmp/logout_test.txt', "Déconnexion détectée ! " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

        // TEST : Logger un message dans les logs Symfony
        $this->logger->info("🚀 Déconnexion détectée !");
    }
}


