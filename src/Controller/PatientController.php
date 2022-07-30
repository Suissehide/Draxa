<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;

use App\Form\PatientCreationFormType;
use App\Form\PatientType;
use App\Form\RendezVousType;

use App\Repository\PatientRepository;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/patient")
 */
class PatientController extends AbstractController
{
    /**
     * @Route("/", name="patient_list", methods="GET|POST")
     */

    public function patient_list(PatientRepository $patientRepository, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $etat = $request->request->get('etat');
            $current = $request->request->get('current');
            $rowCount = $request->request->get('rowCount');
            $searchPhrase = $request->request->get('searchPhrase');
            $sort = $request->request->get('sort');

            $patients = $patientRepository->findByFilter($sort, $searchPhrase, $etat);
            if ($searchPhrase != "" || $sort != "all") {
                $count = count($patients->getQuery()->getResult());
            } else {
                $count = $patientRepository->compte();
            }
            if ($rowCount != -1) {
                $min = ($current - 1) * $rowCount;
                $max = $rowCount;
                $patients->setMaxResults($max)->setFirstResult($min);
            }
            $patients = $patients->getQuery()->getResult();
            $rows = array();
            foreach ($patients as $patient) {
                $rdv = $this->getNextRdv($patient);

                $observ = $patient->getObserv() ? 1 : 0;
                $divers = $patient->getDivers() ? 1 : 0;
                $sortie = 0;
                if ($patient->getDentree())
                    $sortie = 1;
                else if ($patient->getProgetp() == "ViVa module AOMI")
                    $sortie = 2;
                $row = array(
                    "id" => $patient->getId(),
                    "nom" => $patient->getNom(),
                    "prenom" => $patient->getPrenom(),
                    "tel1" => wordwrap($patient->getTel1(), 2, ' ', true),
                    "etp" => $patient->getEtp(),
                    "objectif" => $patient->getObjectif(),
                    "date" => $rdv[1],
                    "categorie" => $rdv[0],
                    "thematique" => $rdv[3],
                    "venu" => $rdv[4],
                    "status" => $sortie,
                    "observ" => $observ,
                    "divers" => $divers
                );
                array_push($rows, $row);
            }

            if ($sort && key($sort) == 'heure')
                usort($rows, $this->build_sorter_heure("date", key($sort), reset($sort)));
            else if ($sort && key($sort) == 'date')
                usort($rows, $this->build_sorter_date(key($sort), reset($sort)));

            $data = array(
                "current" => intval($current),
                "rowCount" => intval($rowCount),
                "rows" => $rows,
                "total" => intval($count)
            );
            return new JsonResponse($data);
        }

        return $this->render('patient/list/index.html.twig', [
            'title' => 'Liste des patients',
            'controller_name' => 'PatientController',
            'patients' => $patientRepository->findAll()
        ]);
    }

    private function build_sorter_heure($date, $hour, $dir='ASC') {
        return function ($a, $b) use ($date, $hour, $dir) {
            $date1 = is_array($a) ? $a[$date] : $a->$date;
            $date2 = is_array($b) ? $b[$date] : $b->$date;
            $time1 = is_array($a) ? $a[$hour] : $a->$hour;
            $time2 = is_array($b) ? $b[$hour] : $b->$hour;
            $time1 = (($time1 == '' && strtoupper($dir) == 'ASC') ? '23:59' : $time1);
            $time1 = (($time1 == '' && strtoupper($dir) == 'DESC') ? '00:00' : $time1);
            $time2 = (($time2 == '' && strtoupper($dir) == 'ASC') ? '23:59' : $time2);
            $time2 = (($time2 == '' && strtoupper($dir) == 'DESC') ? '00:00' : $time2);
            $t1 = date_create_from_format("d/m/Y H:i", (($date1 == '' && strtoupper($dir) == 'ASC') ? '01/01/2999' : $date1) . ' ' . $time1);
            $t2 = date_create_from_format("d/m/Y H:i", (($date2 == '' && strtoupper($dir) == 'ASC') ? '01/01/2999' : $date2) . ' ' . $time2);
            if ($t1 == $t2) return 0;
            return (strtoupper($dir) == 'ASC' ? ($t1 < $t2) : ($t1 > $t2)) ? -1 : 1;
        };
    }

    private function build_sorter_date($key, $dir='ASC') {
        return function ($a, $b) use ($key, $dir) {
            $t1 = is_array($a) ? $a[$key] : $a->$key;
            $t2 = is_array($b) ? $b[$key] : $b->$key;
            $t1 = date_create_from_format("d/m/Y", ($t1 == '' && strtoupper($dir) == 'ASC') ? '01/01/2999' : $t1);
            $t2 = date_create_from_format("d/m/Y", ($t2 == '' && strtoupper($dir) == 'ASC') ? '01/01/2999' : $t2);
            if ($t1 == $t2) return 0;
            return (strtoupper($dir) == 'ASC' ? ($t1 < $t2) : ($t1 > $t2)) ? -1 : 1;
        };
    }

    private function getNextRdv(Patient $patient)
    {
        $em = $this->getDoctrine()->getManager();
        $date = \DateTime::createFromFormat("Y-m-d H:i:s", date(date('Y-m-d') . " 00:00:00"));

        $rendezVous = $em->getRepository(RendezVous::class)->findClosestRdv($date, $patient->getId());

        $rdv = ["", "", "", "", ""];
        if ($rendezVous) {
            $rdv = [
                $rendezVous->getCategorie(),
                $rendezVous->getDate()->format('d/m/Y'),
                $rendezVous->getHeure() ? $rendezVous->getHeure()->format('H:i') : "",
                $rendezVous->getSlot() ? $rendezVous->getSlot()->getThematique() : "",
                $rendezVous->getEtat() ? $rendezVous->getEtat() : "",
            ];
        }

        return $rdv;
    }

    /**
     * @Route("/add", name="patient_add", methods="GET|POST")
     */
    public function add(Request $request): Response
    {
        $patient = new Patient();
        $form = $this->createForm(PatientCreationFormType::class, $patient);

        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $patient = $form->getData();

                $em->persist($patient);
                $em->flush();
            }

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
    public function patient_vue(Patient $patient, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PatientCreationFormType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $patient = $form->getData();

                $em->flush();
            }

            return $this->redirectToRoute('patient_list');
        }

        $rendezVous = new RendezVous();
        $formRendezVous = $this->createForm(RendezVousType::class, $rendezVous);

        $consultations = [];
        foreach (ThematiqueConstants::CONSULTATION as $consultation) { $consultations[] = $consultation; }
        $entretiens = [];
        foreach (ThematiqueConstants::ENTRETIEN as $entretien) { $entretiens[] = $entretien; }
        $ateliers = [];
        foreach (ThematiqueConstants::ATELIER as $atelier) { $ateliers[] = $atelier; }
        $coachings = [];
        foreach (ThematiqueConstants::COACHING as $coaching) { $coachings[] = $coaching; }
        $educatives = [];
        $thematiques = array( $consultations, $entretiens, $ateliers, $coachings, $educatives );

        return $this->render('patient/vue/index.html.twig', [
            'title' => 'Vue',
            'controller_name' => 'PatientController',
            'patient' => $patient,
            'form' => $form->createView(),

            'formRendezVous' => $formRendezVous->createView(),

            'dates_ateliers' => $em->getRepository(Slot::class)->findAllDates("Atelier"),
            'dates_consultations' => $em->getRepository(Slot::class)->findAllDates("Consultation"),
            'dates_entretiens' => $em->getRepository(Slot::class)->findAllDates("Entretien"),
            'dates_educatives' => $em->getRepository(Slot::class)->findAllDates("Educative"),
            'dates_coachings' => $em->getRepository(Slot::class)->findAllDates("Coaching"),

            'thematiques' => $thematiques
        ]);
    }

        /**
     * @Route("/vue/{id}/diagnostic", name="patient_vue_diagnostic", methods="GET|POST")
     */
    public function patient_vue_diagnostic(Patient $patient, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PatientDiagnosticType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $patient = $form->getData();

                $em->flush();
            }

            return $this->redirectToRoute('patient_vue_diagnostic');
        }

        return $this->render('patient/vue/diagostic.html.twig', [
            'title' => 'Diagnostic',
            'controller_name' => 'PatientDiagnosticController',
            'patient' => $patient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/csv", name="csv", methods="GET|POST")
     *
     */
    public function generateCsvAction(PatientRepository $patientRepository): Response
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoder = new CsvEncoder();
        $normalizer = new PropertyNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer), array($encoder));

        $callback = function ($dateTime) {
            return $dateTime instanceof \DateTime
            ? $dateTime->format('d/m/y')
            : '';
        };

        $callback2 = function ($dateTime) {
            return $dateTime instanceof \DateTime
            ? $dateTime->format('H:i')
            : '';
        };

        $normalizer->setCallbacks(array(
            'date' => $callback,
            'dentree' => $callback,
            'dedate' => $callback,
            'date_repro' => $callback,
            'heure' => $callback2
        ));

        $org = $patientRepository->findAll();
        $data = $serializer->serialize($org, 'csv', ['groups' => ['patient']]);

        $data = str_replace(",", ";", $data);
        $data = str_replace("Artisans;", "Artisans,", $data);
        $data = str_replace("observ;divers;sexe;nom;prenom;date;tel1;tel2;distance;etude;profession;activite;diagnostic;dedate;orientation;etpdecision;progetp;precisions;precisionsperso;objectif;dentree;motif;etp;"
        , "Suivi à régulariser;Divers;Sexe;Nom;Prénom;Date de naissance;Téléphone 1;Téléphone 2;Distance d'habitation;Niveau d'étude;Profession;Activité actuelle;Diagnostic médical;Date d'entrée;Orientation;ETP Décision;Type de programme;Précision non inclusion;Précision contenu personnalisé;Objectif;Date de sortie;Motif d'arrêt de programme;Point final parcours ETP;", $data);

        $fileName = "export_patients_" . date("d_m_Y") . ".csv";
        $response = new Response($data);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8; application/excel');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileName);
        echo "\xEF\xBB\xBF"; // UTF-8 with BOM
        return $response;
    }

    /**
     * @Route("/", name="patient_index", methods="GET")
     */
    public function index(PatientRepository $patientRepository): Response
    {
        return $this->render('patient/index.html.twig', ['patients' => $patientRepository->findAll()]);
    }

    /**
     * @Route("/new", name="patient_new", methods="GET|POST")
     */
    function new (Request $request): Response {
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($patient);
            $em->flush();

            return $this->redirectToRoute('patient_index');
        }

        return $this->render('patient/new.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="patient_show", methods="GET")
     */
    public function show(Patient $patient): Response
    {
        return $this->render('patient/show.html.twig', ['patient' => $patient]);
    }

    /**
     * @Route("/{id}/edit", name="patient_edit", methods="GET|POST")
     */
    public function edit(Request $request, Patient $patient): Response
    {
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('patient_edit', ['id' => $patient->getId()]);
        }

        return $this->render('patient/edit.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="patient_delete", methods="DELETE")
     */
    public function delete(Request $request, Patient $patient): Response
    {
        if ($this->isCsrfTokenValid('delete' . $patient->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($patient);
            $em->flush();
        }

        return $this->redirectToRoute('patient_index');
    }
}
