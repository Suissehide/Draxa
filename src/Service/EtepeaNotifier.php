<?php

namespace App\Service;

use App\Entity\RendezVous;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Temporaire : pousse chaque rendez-vous créé vers Etepea, qui l'affiche « à reporter à la main ».
 * Sans URL ou sans jeton (variables vides), rien n'est envoyé.
 * Un échec ne fait jamais échouer la création dans Draxa : il est seulement journalisé.
 */
class EtepeaNotifier
{
    private const TYPES = ['tel' => 'telephonic', 'ambu' => 'ambulatory', 'hospit' => 'hospital'];

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        #[Autowire(env: 'ETEPEA_LEGACY_URL')] private string $url,
        #[Autowire(env: 'ETEPEA_LEGACY_TOKEN')] private string $token,
    ) {
    }

    public function rendezVousCree(RendezVous $rdv): void
    {
        if ($this->url === '' || $this->token === '') {
            return;
        }

        try {
            $response = $this->httpClient->request('POST', $this->url, [
                'headers' => ['X-Etepea-Token' => $this->token],
                'json' => self::payload($rdv),
                'timeout' => 2,
                'max_duration' => 2,
            ]);
            $status = $response->getStatusCode();
            if ($status !== 204) {
                $this->logger->warning('Envoi du RDV à Etepea refusé', ['rdv' => $rdv->getId(), 'statut' => $status]);
            }
        } catch (\Throwable $e) {
            $this->logger->warning('Envoi du RDV à Etepea échoué', ['rdv' => $rdv->getId(), 'erreur' => $e->getMessage()]);
        }
    }

    /**
     * Corps attendu par Etepea (rejouable : legacyId sert de clé).
     */
    public static function payload(RendezVous $rdv): array
    {
        $slot = $rdv->getSlot();
        $debut = $slot?->getHeureDebut();
        $fin = $slot?->getHeureFin();
        // Heures seules : les dates portées par des champs TIME ne sont pas fiables.
        $minutes = fn (\DateTimeInterface $t) => (int) $t->format('H') * 60 + (int) $t->format('i');
        $duree = $debut && $fin ? $minutes($fin) - $minutes($debut) : 0;
        $patient = $rdv->getPatient();
        $type = mb_strtolower(trim((string) ($rdv->getType() ?: $slot?->getType())));
        $thematique = trim((string) ($rdv->getThematique() ?: $slot?->getThematique() ?: $rdv->getCategorie()));

        return [
            'legacyId' => (string) $rdv->getId(),
            'date' => $rdv->getDate()?->format('Y-m-d'),
            'time' => $rdv->getHeure()?->format('H:i'),
            'durationMinutes' => $duree > 0 ? $duree : null,
            'patientName' => trim(mb_strtoupper((string) $patient?->getNom()) . ' ' . $patient?->getPrenom()),
            'thematic' => $thematique !== '' ? $thematique : null,
            'type' => self::TYPES[$type] ?? null,
        ];
    }
}
