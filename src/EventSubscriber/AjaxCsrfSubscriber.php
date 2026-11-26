<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Exige l'en-tête X-CSRF-Token (jeton « ajax », voir base.html.twig et public/js/top.js)
 * sur toute requête qui modifie des données. Protégé par défaut : une nouvelle route l'est d'office.
 */
class AjaxCsrfSubscriber implements EventSubscriberInterface
{
    /** Routes soumises par des formulaires classiques qui portent déjà leur propre jeton. */
    private const FORM_ROUTES = ['login', 'register', 'patient_add', 'patient_vue', 'patient_edit_diagnostic', 'patient_delete'];

    public function __construct(private CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        // Après le routeur (32) pour connaître la route.
        return [KernelEvents::REQUEST => ['onKernelRequest', 0]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (
            !$event->isMasterRequest()
            || $request->isMethodSafe()
            || in_array($request->attributes->get('_route'), self::FORM_ROUTES, true)
        ) {
            return;
        }

        $token = new CsrfToken('ajax', (string) $request->headers->get('X-CSRF-Token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            $event->setResponse(new JsonResponse(['error' => 'Jeton CSRF invalide, rechargez la page.'], Response::HTTP_FORBIDDEN));
        }
    }
}
