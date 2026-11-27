<?php

namespace App\Tests;

use App\Controller\PatientController;
use App\Entity\AppUser;
use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Semaine;
use App\Entity\Slot;
use App\Entity\Soignant;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Parcourt les écrans et endpoints AJAX principaux sur une vraie base MySQL.
 *
 * La base est RECRÉÉE : DATABASE_URL doit pointer vers une base dont le nom finit par « _test ».
 *   DATABASE_URL='mysql://root:root@127.0.0.1:3306/draxa_test?serverVersion=8.4' vendor/bin/phpunit
 */
class SmokeTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private string $csrf = '';

    public function testSortRowsPutsEmptyValuesLast(): void
    {
        $rows = [['date' => ''], ['date' => '02/01/2025'], ['date' => '31/12/2024']];

        $this->assertSame(['31/12/2024', '02/01/2025', ''], array_column(PatientController::sortRows($rows, 'date', 'ASC'), 'date'));
        $this->assertSame(['02/01/2025', '31/12/2024', ''], array_column(PatientController::sortRows($rows, 'date', 'DESC'), 'date'));
    }

    public function testApplicationFlows(): void
    {
        $url = (string) getenv('DATABASE_URL');
        if (!preg_match('#/\w+_test(\?|$)#', $url)) {
            $this->markTestSkipped('DATABASE_URL vers une base *_test requise.');
        }

        // Etepea configuré mais injoignable : la création des RDV doit réussir quand même.
        $_SERVER['ETEPEA_LEGACY_URL'] = $_ENV['ETEPEA_LEGACY_URL'] = 'http://127.0.0.1:9/legacy-appointments';
        $_SERVER['ETEPEA_LEGACY_TOKEN'] = $_ENV['ETEPEA_LEGACY_TOKEN'] = 'test';

        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $tool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);

        [$patient, $slot, $semaine] = $this->seed();
        // Non connecté : page → connexion, appel AJAX → 401 (le script affiche alors « rechargez la page »).
        $this->client->request('GET', '/accueil/');
        $this->assertResponseRedirects('/login');
        $this->client->xmlHttpRequest('POST', '/patient/', ['current' => 1]);
        $this->assertResponseStatusCodeSame(401);

        // Mauvais mot de passe : message en français, identifiant conservé.
        $form = $this->client->request('GET', '/login')->selectButton('Se connecter')->form(['_username' => 'admin', '_password' => 'mauvais']);
        $this->client->submit($form);
        $page = $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-msg', 'Identifiant ou mot de passe incorrect.');
        $this->assertSame('admin', $page->filter('#inputUsername')->attr('value'));

        $this->login();

        // CSRF : jeton lu dans la page comme le fait le navigateur ; sans jeton → 403, GET sur une action → 405.
        $crawler = $this->client->request('GET', '/accueil/');
        $this->assertResponseIsSuccessful(substr((string) $this->client->getResponse()->getContent(), 0, 3000));
        $this->csrf = $crawler->filter('meta[name="csrf-token"]')->attr('content');
        $this->client->xmlHttpRequest('POST', '/todo/add', ['description' => 'x']);
        $this->assertResponseStatusCodeSame(403);
        $this->client->xmlHttpRequest('POST', '/todo/add', ['description' => 'x'], [], ['HTTP_X_CSRF_TOKEN' => 'faux']);
        $this->assertResponseStatusCodeSame(403);
        $this->client->xmlHttpRequest('GET', '/accueil/add_patient_slot');
        $this->assertResponseStatusCodeSame(405);

        // Liste patients : tri base, tri calculé, recherche dans les rendez-vous, tri invalide ignoré.
        foreach ([['nom' => 'asc'], ['date' => 'desc'], ['status' => 'asc'], ['p.id) OR 1=1 --' => 'asc']] as $sort) {
            $data = $this->ajax('POST', '/patient/', ['current' => 1, 'rowCount' => 10, 'etat' => 'all', 'sort' => $sort]);
            $this->assertSame(2, $data['total']);
        }
        // Filtres de la liste : aucun patient « à régulariser » ni en ViVa AOMI dans le jeu de test.
        $this->assertSame(0, $this->ajax('POST', '/patient/', ['current' => 1, 'rowCount' => 10, 'etat' => 'all', 'observ' => '1'])['total']);
        $this->assertSame(0, $this->ajax('POST', '/patient/', ['current' => 1, 'rowCount' => 10, 'etat' => 'all', 'progetp' => 'ViVa module AOMI'])['total']);
        $data = $this->ajax('POST', '/patient/', ['current' => 1, 'rowCount' => 10, 'etat' => 'in', 'searchPhrase' => 'Atelier']);
        $this->assertSame(1, $data['total']);
        $this->assertSame('Atelier', $data['rows'][0]['categorie']);
        $this->assertSame('Tabac', $data['rows'][0]['thematique']);

        // Accueil + grille des créneaux.
        $day = $slot->getDate()->format('d/m/Y');
        $data = $this->ajax('POST', '/accueil/', ['date' => $day]);
        $this->assertCount(1, $data[3]); // ateliers
        $data = $this->ajax('POST', '/accueil/slot', ['current' => 1, 'rowCount' => 10, 'categories' => ['Atelier'], 'sort' => ['horaire' => 'desc']]);
        $this->assertSame(1, $data['total']);
        $data = $this->ajax('POST', '/accueil/slot', ['current' => 1, 'rowCount' => 10, 'sort' => ['soignant' => 'asc'], 'searchPhrase' => 'Dupont']);
        $this->assertSame(1, $data['total']);

        // Présence et note de transmission modifiées depuis l'agenda.
        $rdv = $this->em->getRepository(RendezVous::class)->findOneBy([]);
        $this->assertTrue($this->ajax('POST', '/rendez_vous/etat', ['id' => $rdv->getId(), 'etat' => 'Oui']));
        $this->assertTrue($this->ajax('POST', '/rendez_vous/notes', ['id' => $rdv->getId(), 'notes' => 'À rappeler']));
        $this->em->clear();
        $rdv = $this->em->find(RendezVous::class, $rdv->getId());
        $this->assertSame(['Oui', 'À rappeler'], [$rdv->getEtat(), $rdv->getNotes()]);
        $this->client->xmlHttpRequest('POST', '/rendez_vous/etat', ['id' => $rdv->getId(), 'etat' => 'Peut-être'], [], ['HTTP_X_CSRF_TOKEN' => $this->csrf]);
        $this->assertResponseStatusCodeSame(404);

        // Ajout d'un patient au créneau, doublon refusé.
        $this->assertNotFalse($this->ajax('POST', '/accueil/add_patient_slot', ['slotId' => $slot->getId(), 'patientId' => $patient->getId() + 1]));
        $this->assertFalse($this->ajax('POST', '/accueil/add_patient_slot', ['slotId' => $slot->getId(), 'patientId' => $patient->getId() + 1]));

        // Ajout depuis la fiche patient (fenêtre « ajouter un rendez-vous »).
        $rdvId = $this->ajax('POST', '/rendez_vous/add', [
            'id' => $patient->getId(), 'slotId' => $slot->getId(), 'categorie' => 'Atelier',
            'date' => $slot->getDate()->format('d/m/Y'), 'time' => '09:00', 'thematique' => 'Tabac',
        ]);
        $this->assertIsInt($rdvId);

        // Semaines : création, doublon (crashait en 500), duplication avec créneaux.
        $this->assertIsInt($this->ajax('POST', '/semaine/add', ['dateDebut' => '05/01/2030', 'dateFin' => '11/01/2030']));
        $this->assertFalse($this->ajax('POST', '/semaine/add', ['dateDebut' => '05/01/2030', 'dateFin' => '11/01/2030']));
        $data = $this->ajax('POST', '/semaine/duplicate/' . $semaine->getId(), ['dateDebut' => '12/01/2030', 'dateFin' => '18/01/2030']);
        $this->assertSame('13/01/2030', $data['slots'][0]['date']);

        // Duplication de créneau : l'original ne doit pas bouger.
        $data = $this->ajax('POST', '/slot/duplicate/' . $slot->getId());
        $this->assertSame(['10:00', '11:00'], [$data['debut'], $data['fin']]);
        $this->em->clear();
        $this->assertSame('10:00', $this->em->find(Slot::class, $slot->getId())->getHeureFin()->format('H:i'));
        $this->assertSame([], $this->em->find(Slot::class, $data['id'])->getRendezVous()->toArray());

        // Statistiques : le rendez-vous du premier jour de la période doit être compté.
        $data = $this->ajax('POST', '/statistiques', ['dateStart' => $day, 'dateEnd' => $day]);
        $this->assertSame(1, $data['slots']['ateliers']['tabac']['oui']);

        // Soignants, todo, export, 404.
        $this->assertTrue($this->ajax('POST', '/soignant/update', ['soignants' => [1], 'status' => 'true']));
        $id = $this->ajax('POST', '/todo/add', ['description' => '<b>x</b>']);
        $this->assertTrue($this->ajax('POST', '/todo/enable', ['taskId' => $id, 'enable' => 'true']));
        $this->assertTrue($this->ajax('POST', '/todo/clear'));
        $this->client->request('GET', '/patient/csv');
        $this->assertResponseIsSuccessful();
        // Réponse en flux : le contenu est dans la réponse interne du client.
        $csv = $this->client->getInternalResponse()->getContent();
        $this->assertStringContainsString('Nom;Prénom', $csv);
        $this->assertStringContainsString('RDV 1 date', $csv);
        $this->client->request('GET', '/patient/vue/999999');
        $this->assertResponseStatusCodeSame(404);

        foreach (['/accueil/', '/patient/', '/patient/vue/' . $patient->getId(), '/statistiques', '/settings/2026', '/soignant/', '/annee/', '/patient/vue/' . $patient->getId() . '/diagnostic'] as $page) {
            $this->client->request('GET', $page);
            $this->assertResponseIsSuccessful($page);
        }
    }

    private function seed(): array
    {
        $soignant = (new Soignant())->setNom('Dupont')->setPrenom('Anne')->setStatus(true)->setPriority(0);
        $semaine = (new Semaine())->setDateDebut(new \DateTime('2030-01-01'))->setDateFin(new \DateTime('2030-01-04'));
        $slot = (new Slot())
            ->setDate(new \DateTime('2030-01-02'))
            ->setHeureDebut(new \DateTime('09:00'))
            ->setHeureFin(new \DateTime('10:00'))
            ->setCategorie('Atelier')
            ->setThematique('Tabac')
            ->setPlace(5)
            ->setSoignant($soignant);
        $semaine->addSlot($slot);

        $patient = (new Patient())->setNom('Martin')->setPrenom('Paul');
        $sorti = (new Patient())->setNom('Bernard')->setPrenom('Lea')->setDentree(new \DateTime('2025-06-01'));
        $rdv = (new RendezVous())
            ->setPatient($patient)
            ->setSlot($slot)
            ->setDate($slot->getDate())
            ->setCategorie('Atelier')
            ->setThematique('Tabac')
            ->setEtat('Oui');

        $user = (new AppUser())->setUsername('admin')->setPassword('x')->setRoles(['ROLE_ADMIN']);

        foreach ([$soignant, $semaine, $patient, $sorti, $rdv, $user] as $entity) {
            $this->em->persist($entity);
        }
        $this->em->flush();

        return [$patient, $slot, $semaine];
    }

    private function login(): void
    {
        $this->client->loginUser($this->em->getRepository(AppUser::class)->findOneBy(['username' => 'admin']));
    }

    private function ajax(string $method, string $uri, array $params = []): mixed
    {
        $this->client->xmlHttpRequest($method, $uri, $params, [], ['HTTP_X_CSRF_TOKEN' => $this->csrf]);
        $this->assertResponseIsSuccessful($uri . ' : ' . substr((string) $this->client->getResponse()->getContent(), 0, 500));

        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
