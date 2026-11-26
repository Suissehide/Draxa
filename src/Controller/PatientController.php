<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;
use App\Entity\DiagnosticEducatif;
use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;
use App\Form\DiagnosticEducatifType;
use App\Form\PatientCreationFormType;
use App\Form\RendezVousType;
use App\Repository\PatientRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/patient")
 */
class PatientController extends AbstractController
{
    /** En-têtes lisibles de l'export CSV (attribut => libellé). */
    private const CSV_LABELS = [
        'observ' => 'Suivi à régulariser',
        'divers' => 'Divers',
        'notes' => 'Notes',
        'sexe' => 'Sexe',
        'nom' => 'Nom',
        'prenom' => 'Prénom',
        'date' => 'Date de naissance',
        'tel1' => 'Téléphone 1',
        'tel2' => 'Téléphone 2',
        'distance' => "Distance d'habitation",
        'etude' => "Niveau d'étude",
        'profession' => 'Profession',
        'activite' => 'Activité actuelle',
        'diagnostic' => 'Diagnostic médical',
        'dedate' => "Date d'entrée",
        'orientation' => 'Orientation',
        'etpdecision' => 'ETP Décision',
        'progetp' => 'Type de programme',
        'precisions' => 'Précision non inclusion',
        'precisionsperso' => 'Précision contenu personnalisé',
        'objectif' => 'Objectif',
        'dentree' => 'Date de sortie',
        'motif' => "Motif d'arrêt de programme",
        'etp' => 'Point final parcours ETP',
    ];

    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @Route("/", name="patient_list", methods="GET|POST")
     */
    public function patient_list(PatientRepository $patientRepository, Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('patient/list/index.html.twig', [
                'title' => 'Liste des patients',
                'controller_name' => 'PatientController',
            ]);
        }

        $current = max(1, (int) $request->get('current', 1));
        $rowCount = (int) $request->get('rowCount', -1);
        $sort = (array) $request->get('sort');
        $sortKey = $sort ? (string) key($sort) : null;
        $sortDir = strtoupper((string) reset($sort)) === 'DESC' ? 'DESC' : 'ASC';

        $qb = $patientRepository->createFilterQueryBuilder(
            (string) $request->get('searchPhrase', ''),
            (string) $request->get('etat', 'in'),
            $sortKey,
            $sortDir
        );
        $count = $patientRepository->countFiltered($qb);

        // Tri sur une colonne calculée (prochain rendez-vous, statut) : il faut tout charger puis paginer.
        $sortInPhp = $sortKey !== null && !in_array($sortKey, PatientRepository::SORTABLE, true);
        if ($rowCount > 0 && !$sortInPhp) {
            $qb->setFirstResult(($current - 1) * $rowCount)->setMaxResults($rowCount);
        }

        $patients = $qb->getQuery()->getResult();
        $nextRdvs = $this->em->getRepository(RendezVous::class)->findNextByPatients(
            array_map(fn (Patient $p) => $p->getId(), $patients),
            new \DateTime('today')
        );

        $rows = [];
        foreach ($patients as $patient) {
            $rdv = $nextRdvs[$patient->getId()] ?? null;
            $rows[] = [
                'id' => $patient->getId(),
                'nom' => $patient->getNom(),
                'prenom' => $patient->getPrenom(),
                'tel1' => wordwrap((string) $patient->getTel1(), 2, ' ', true),
                'etp' => $patient->getEtp(),
                'objectif' => $patient->getObjectif(),
                'date' => $rdv?->getDate()?->format('d/m/Y') ?? '',
                'categorie' => $rdv?->getCategorie() ?? '',
                'thematique' => $rdv?->getSlot()?->getThematique() ?? '',
                'venu' => $rdv?->getEtat() ?: '',
                'status' => $patient->getDentree() ? 1 : ($patient->getProgetp() === 'ViVa module AOMI' ? 2 : 0),
                'observ' => $patient->getObserv() ? 1 : 0,
                'divers' => $patient->getDivers() ? 1 : 0,
            ];
        }

        if ($sortInPhp) {
            $rows = self::sortRows($rows, $sortKey, $sortDir);
            if ($rowCount > 0) {
                $rows = array_slice($rows, ($current - 1) * $rowCount, $rowCount);
            }
        }

        return new JsonResponse(self::utf8Clean([
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $count,
        ]));
    }

    /**
     * Trie les lignes de la grille ; les valeurs vides sont toujours placées en dernier.
     * Les dates « d/m/Y » sont comparées chronologiquement.
     */
    public static function sortRows(array $rows, string $key, string $dir): array
    {
        $value = function (array $row) use ($key) {
            $v = $row[$key] ?? '';
            if ($key === 'date' && $v !== '') {
                $v = implode('', array_reverse(explode('/', $v)));
            }

            return $v;
        };

        usort($rows, function (array $a, array $b) use ($value, $dir) {
            $va = $value($a);
            $vb = $value($b);
            if ($va === '' || $vb === '') {
                return ($va === '') <=> ($vb === '');
            }

            return $dir === 'DESC' ? $vb <=> $va : $va <=> $vb;
        });

        return $rows;
    }

    /**
     * Remplace les séquences UTF-8 invalides (anciennes données) pour que json_encode n'échoue pas.
     */
    private static function utf8Clean(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map([self::class, 'utf8Clean'], $data);
        }

        return is_string($data) ? mb_convert_encoding($data, 'UTF-8', 'UTF-8') : $data;
    }

    /**
     * @Route("/add", name="patient_add", methods="GET|POST")
     */
    public function add(Request $request): Response
    {
        $patient = new Patient();
        $form = $this->createForm(PatientCreationFormType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('validation')->isClicked()) {
                return $this->redirectToRoute('patient_list');
            }

            $this->em->persist($patient);
            $this->em->flush();

            return $this->redirectToRoute('patient_vue', ['id' => $patient->getId()]);
        }

        return $this->render('patient/create/index.html.twig', [
            'title' => 'Create',
            'controller_name' => 'PatientController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/vue/{id}", name="patient_vue", methods="GET|POST")
     */
    public function patient_vue(int $id, Request $request): Response
    {
        $patient = $this->findPatient($id);
        $form = $this->createForm(PatientCreationFormType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $this->em->flush();
            }

            return $this->redirectToRoute('patient_list');
        }

        $formRendezVous = $this->createForm(RendezVousType::class, new RendezVous());
        $slotRepository = $this->em->getRepository(Slot::class);

        return $this->render('patient/vue/index.html.twig', [
            'title' => 'Vue',
            'controller_name' => 'PatientController',
            'patient' => $patient,
            'form' => $form->createView(),
            'formRendezVous' => $formRendezVous->createView(),
            'dates_ateliers' => $slotRepository->findAllDates('Atelier'),
            'dates_consultations' => $slotRepository->findAllDates('Consultation'),
            'dates_entretiens' => $slotRepository->findAllDates('Entretien'),
            'dates_educatives' => $slotRepository->findAllDates('Educative'),
            'dates_coachings' => $slotRepository->findAllDates('Coaching'),
            'thematiques' => ThematiqueConstants::lists(),
        ]);
    }

    /**
     * @Route("/edit/{id}/diagnostic", name="patient_edit_diagnostic", methods="GET|POST")
     */
    public function patient_edit_diagnostic(int $id, Request $request): Response
    {
        $patient = $this->findPatientWithDiagnostic($id);
        $form = $this->createForm(DiagnosticEducatifType::class, $patient->getDiagnosticEducatif());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $this->em->flush();
            }

            return $this->redirectToRoute('patient_vue_diagnostic', ['id' => $id]);
        }

        return $this->render('patient/vue/diagnostic_educatif_edit.html.twig', [
            'title' => 'Diagnostic éducatif',
            'controller_name' => 'PatientDiagnosticController',
            'patient' => $patient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/vue/{id}/diagnostic", name="patient_vue_diagnostic", methods="GET")
     */
    public function patient_vue_diagnostic(int $id): Response
    {
        return $this->render('patient/vue/diagnostic_educatif_vue.html.twig', [
            'title' => 'Diagnostic éducatif',
            'controller_name' => 'PatientDiagnosticController',
            'patient' => $this->findPatientWithDiagnostic($id),
        ]);
    }

    /**
     * @Route("/csv", name="csv", methods="GET")
     */
    public function generateCsvAction(PatientRepository $patientRepository): Response
    {
        $normalizer = new PropertyNormalizer(new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())));
        $date = fn ($d) => $d instanceof \DateTimeInterface ? $d->format('d/m/y') : '';
        $normalizer->setCallbacks([
            'date' => $date,
            'dentree' => $date,
            'dedate' => $date,
            'date_repro' => $date,
            'heure' => fn ($d) => $d instanceof \DateTimeInterface ? $d->format('H:i') : '',
        ]);
        $serializer = new Serializer([$normalizer], [new CsvEncoder()]);

        $csv = $serializer->serialize($patientRepository->findAll(), 'csv', [
            'groups' => ['patient'],
            CsvEncoder::DELIMITER_KEY => ';',
        ]);

        // Remplace la ligne d'en-tête technique par des libellés lisibles.
        [$header, $body] = explode("\n", $csv, 2) + ['', ''];
        $labels = array_map(
            fn ($col) => self::CSV_LABELS[$col] ?? $col,
            str_getcsv($header, ';')
        );
        $csv = implode(';', $labels) . "\n" . $body;

        $response = new Response("\xEF\xBB\xBF" . $csv); // BOM : Excel détecte l'UTF-8
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'export_patients_' . date('d_m_Y') . '.csv'
        ));

        return $response;
    }

    /**
     * @Route("/{id}", name="patient_delete", methods="DELETE")
     */
    public function delete(Request $request, int $id): Response
    {
        $patient = $this->findPatient($id);
        if ($this->isCsrfTokenValid('delete' . $patient->getId(), $request->request->get('_token'))) {
            $this->em->remove($patient);
            $this->em->flush();
        }

        return $this->redirectToRoute('patient_list');
    }

    private function findPatient(int $id): Patient
    {
        return $this->em->getRepository(Patient::class)->find($id)
            ?? throw $this->createNotFoundException('Patient introuvable');
    }

    private function findPatientWithDiagnostic(int $id): Patient
    {
        $patient = $this->findPatient($id);
        if (!$patient->getDiagnosticEducatif()) {
            $patient->setDiagnosticEducatif(new DiagnosticEducatif());
            $this->em->flush();
        }

        return $patient;
    }
}
