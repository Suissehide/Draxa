<?php

namespace App\Tests;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;
use App\Service\EtepeaNotifier;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class EtepeaNotifierTest extends TestCase
{
    public function testSendsExpectedPayload(): void
    {
        $requests = [];
        $client = new MockHttpClient(function (string $method, string $url, array $options) use (&$requests) {
            $requests[] = [$method, $url, $options];

            return new MockResponse('', ['http_code' => 204]);
        });
        $logger = $this->logger();

        (new EtepeaNotifier($client, $logger, 'https://etepea.test/legacy-appointments', 'secret'))->rendezVousCree($this->rdv());

        $this->assertCount(1, $requests);
        [$method, $url, $options] = $requests[0];
        $this->assertSame(['POST', 'https://etepea.test/legacy-appointments'], [$method, $url]);
        $this->assertContains('X-Etepea-Token: secret', $options['headers']);
        $this->assertSame([
            'legacyId' => '42',
            'date' => '2030-01-02',
            'time' => '09:00',
            'durationMinutes' => 90,
            'patientName' => 'MARTIN Paul',
            'thematic' => 'Atelier', // ni RDV ni créneau : catégorie
            'type' => 'telephonic',  // vide sur le RDV : celui du créneau, « Tel »
        ], json_decode($options['body'], true));
        $this->assertSame([], $logger->records);
    }

    public function testFailuresOnlyLogAWarning(): void
    {
        $logger = $this->logger();
        $responses = [new MockResponse('', ['http_code' => 401]), new MockResponse('', ['error' => 'timeout'])];
        $notifier = new EtepeaNotifier(new MockHttpClient($responses), $logger, 'https://etepea.test/x', 'secret');

        $notifier->rendezVousCree($this->rdv());
        $notifier->rendezVousCree($this->rdv());

        $this->assertSame(['warning', 'warning'], array_column($logger->records, 0));
    }

    public function testDisabledWhenNotConfigured(): void
    {
        $client = new MockHttpClient(fn () => throw new TransportException('ne doit pas être appelé'));

        (new EtepeaNotifier($client, $this->logger(), '', 'secret'))->rendezVousCree($this->rdv());

        $this->assertSame(0, $client->getRequestsCount());
    }

    private function rdv(): RendezVous
    {
        $slot = (new Slot())
            ->setHeureDebut(new \DateTime('09:00'))
            ->setHeureFin(new \DateTime('10:30'))
            ->setType('Tel');
        $rdv = (new RendezVous())
            ->setSlot($slot)
            ->setPatient((new Patient())->setNom('Martin')->setPrenom('Paul'))
            ->setDate(new \DateTime('2030-01-02'))
            ->setHeure(new \DateTime('09:00'))
            ->setCategorie('Atelier');
        (new \ReflectionProperty(RendezVous::class, 'id'))->setValue($rdv, 42);

        return $rdv;
    }

    private function logger(): AbstractLogger
    {
        return new class extends AbstractLogger {
            public array $records = [];

            public function log($level, \Stringable|string $message, array $context = []): void
            {
                $this->records[] = [$level, $message];
            }
        };
    }
}
