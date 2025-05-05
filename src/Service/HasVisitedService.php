<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HasVisitedService
{
    private SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function hasVisited(int $businessId): bool
    {
        $visitedBusinesses = $this->session->get('visited_businesses', []);

        if (!in_array($businessId, $visitedBusinesses, true)) {
            $visitedBusinesses[] = $businessId;
            $this->session->set('visited_businesses', $visitedBusinesses);
            return true;
        }

        return false;
    }
}
