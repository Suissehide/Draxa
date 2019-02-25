<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Entretien;
use App\Entity\Patient;
use App\Entity\Telephonique;
use App\Entity\RendezVous;
use App\Form\AtelierType;
use App\Form\EntretienType;
use App\Form\PatientCreationFormType;
use App\Form\PatientType;
use App\Form\TelephoniqueType;
use App\Form\RendezVousType;
use App\Repository\PatientRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
class PatientController extends Controller
{
    /**
     * @Route("/", name="patient", methods="GET|POST")
     */

    public function accueil(PatientRepository $patientRepository, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $etat = $request->request->get('etat');
            $current = $request->request->get('current');
            $rowCount = $request->request->get('rowCount');
            $searchPhrase = $request->request->get('searchPhrase');
            $sort = $request->request->get('sort');

            $patients = $patientRepository->findByFilter($sort, $searchPhrase, $etat);
            if ($searchPhrase != "") {
                $count = count($patients->getQuery()->getResult());
            } else {
                $count = $patientRepository->compte();
            }
            if ($rowCount != -1) {
                $min = ($current - 1) * $rowCount;
                $max = $rowCount;
                $patients->setMaxResults($max)
                    ->setFirstResult($min);
            }
            $patients = $patients->getQuery()->getResult();
            $rows = array();
            foreach ($patients as $patient) {
                $rdv = "rdv";
                $thematique = "thematique";
                $type = "type";
                $sortie = 0;
                $observ = 0;
                if ($patient->getObserv())
                    $observ = 1;
                if ($patient->getDentree())
                    $sortie = 1;
                $row = [
                    "id" => $patient->getId(),
                    "nom" => $patient->getNom(),
                    "prenom" => $patient->getPrenom(),
                    "tel1" => wordwrap($patient->getTel1(), 2, ' ', true),
                    "rdv" => $rdv,
                    "thematique" => $thematique,
                    "type" => $type,
                    "etp" => $patient->getEtp(),
                    "status" => $sortie,
                    "observ" => $observ,
                ];
                array_push($rows, $row);
            }
            $data = array(
                "current" => intval($current),
                "rowCount" => intval($rowCount),
                "rows" => $rows,
                "total" => intval($count)
            );
            return new JsonResponse($data);
        }

        return $this->render('patient/list/index.html.twig', [
            'title' => 'Patients',
            'controller_name' => 'PatientController',
            'patients' => $patientRepository->findAll(),
        ]);
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

            return $this->redirectToRoute('patient');
        }

        return $this->render('patient/create/index.html.twig', [
            'title' => 'Create',
            'controller_name' => 'PatientController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/vue/{id}", name="vue", methods="GET|POST")
     */
    public function vue(Patient $patient, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PatientCreationFormType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $patient = $form->getData();

                $em->flush();
            }

            return $this->redirectToRoute('patient');
        }

        $entretien = new Entretien();
        $formEntretien = $this->createForm(EntretienType::class, $entretien);

        $atelier = new Atelier();
        $formAtelier = $this->createForm(AtelierType::class, $atelier);

        $telephonique = new Telephonique();
        $formTelephonique = $this->createForm(TelephoniqueType::class, $telephonique);

        $rendezVous = new RendezVous();
        $formRendezVous = $this->createForm(RendezVousType::class, $rendezVous);

        return $this->render('patient/vue/index.html.twig', [
            'title' => 'Vue',
            'controller_name' => 'PatientController',
            'patient' => $patient,
            'today' => date("d-m-y"),
            'form' => $form->createView(),
            'formEntretien' => $formEntretien->createView(),
            'formAtelier' => $formAtelier->createView(),
            'formTelephonique' => $formTelephonique->createView(),
            'formRendezVous' => $formRendezVous->createView(),
        ]);
    }

    /**
     * @Route("/csv", name="csv", methods="GET|POST")
     *
     */
    public function generateCsvAction(PatientRepository $patientRepository): Response
    {
        // $data = $this->CallApi('GET', $this->generateUrl('cget', array(), UrlGeneratorInterface::ABSOLUTE_URL).'.csv', false);
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoder = new CsvEncoder();
        $normalizer = new PropertyNormalizer($classMetadataFactory);
        $serializer = new Serializer(array($normalizer), array($encoder));

        $callback = function ($dateTime) {
            return $dateTime instanceof \DateTime
            ? $dateTime->format('d/m/y')
            : '';
        };

        $normalizer->setCallbacks(array(
            'date' => $callback,
            'dentree' => $callback,
            'dedate' => $callback,
            'date_repro' => $callback,
        ));

        $org = $patientRepository->findAll();
        $data = $serializer->serialize($org, 'csv', ['groups' => ['patient']]);

        $data = str_replace(",", ";", $data);
        $data = str_replace("Artisans;", "Artisans,", $data);

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
