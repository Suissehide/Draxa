<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Entretien;
use App\Entity\Patient;
use App\Entity\Telephonique;
use App\Entity\RendezVous;
use App\Entity\BCVs;
use App\Entity\Infos;

use App\Form\AtelierType;
use App\Form\EntretienType;
use App\Form\PatientCreationFormType;
use App\Form\PatientType;
use App\Form\TelephoniqueType;
use App\Form\RendezVousType;
use App\Form\BCVsType;
use App\Form\InfosType;

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
                $rdv = $this->getNextRdv($patient);

                $observ = $patient->getObserv() ? 1 : 0;
                $divers = $patient->getDivers() ? 1 : 0;
                $sortie = 0;
                if ($patient->getDentree())
                    $sortie = 1;
                else if ($patient->getProgetp() == "AOMI")
                    $sortie = 2;
                else if ($patient->getProgetp() == "AOMI + HRCV")
                    $sortie = 3;
                $row = [
                    "id" => $patient->getId(),
                    "nom" => $patient->getNom(),
                    "prenom" => $patient->getPrenom(),
                    "tel1" => wordwrap($patient->getTel1(), 2, ' ', true),
                    "rdv" => $rdv[0],
                    "date" => $rdv[1],
                    "heure" => $rdv[2],
                    "thematique" => $rdv[3],
                    "type" => $rdv[4],
                    "etp" => $patient->getEtp(),
                    "status" => $sortie,
                    "observ" => $observ,
                    "divers" => $divers
                ];
                array_push($rows, $row);
            }

            // dump($rows);

            if ($sort && key($sort) == 'date')
                usort($rows, $this->build_sorter_date(key($sort), reset($sort)));
            if ($sort && key($sort) == 'heure')
                usort($rows, $this->build_sorter_heure("date", key($sort), reset($sort)));


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

    private function build_sorter_heure($date, $hour, $dir='ASC') {
        dump("heure");
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

    private function getNextRdv(Patient $patient)
    {
        $em = $this->getDoctrine()->getManager();
        $date = \DateTime::createFromFormat("Y-m-d H:i:s", date(date('Y-m-d') . " 00:00:00"));

        $rendezVous = $em->getRepository(RendezVous::class)->findClosestRdv($date, $patient->getId());
        $ateliers = $em->getRepository(Atelier::class)->findClosestRdv($date, $patient->getId());
        $telephonique = $em->getRepository(Telephonique::class)->findClosestRdv($date, $patient->getId());
        $entretien = $em->getRepository(Entretien::class)->findClosestRdv($date, $patient->getId());
        $bcvs = $em->getRepository(BCVs::class)->findClosestRdv($date, $patient->getId());
        $infos = $em->getRepository(Infos::class)->findClosestRdv($date, $patient->getId());

        $rdv = ["", "", "", "", ""];
        $min_date = $date->add(new \DateInterval('P1Y'));

        if ($min_date && $rendezVous && date_diff($min_date, date_create($rendezVous->getDate()->format('y-m-d')), false)->invert) {
            $min_date = date_create($rendezVous->getDate()->format('y-m-d'));
            $rdv = ["Consultation", $rendezVous->getDate()->format('d/m/Y'), $rendezVous->getHeure()->format('H:i'), $rendezVous->getThematique(), $rendezVous->getType()];
        }
        if ($min_date && $ateliers && date_diff($min_date, date_create($ateliers->getDate()->format('y-m-d')), false)->invert) {
            $min_date = date_create($ateliers->getDate()->format('y-m-d'));
            $rdv = ["Atelier", $ateliers->getDate()->format('d/m/Y'), $ateliers->getHeure()->format('H:i'), $ateliers->getThematique(), $ateliers->getType()];
        }
        if ($min_date && $telephonique && date_diff($min_date, date_create($telephonique->getDate()->format('y-m-d')), false)->invert) {
            $min_date = date_create($telephonique->getDate()->format('y-m-d'));
            $rdv = ["Téléphonique", $telephonique->getDate()->format('d/m/Y'), "", "", $telephonique->getType()];
        }
        if ($min_date && $entretien && date_diff($min_date, date_create($entretien->getDate()->format('y-m-d')), false)->invert) {
            $min_date = date_create($entretien->getDate()->format('y-m-d'));
            $rdv = ["Entretien", $entretien->getDate()->format('d/m/Y'), $entretien->getHeure()->format('H:i'), $entretien->getThematique(), $entretien->getType()];
        }
        if ($min_date && $bcvs && date_diff($min_date, date_create($bcvs->getDate()->format('y-m-d')), false)->invert) {
            $min_date = date_create($bcvs->getDate()->format('y-m-d'));
            $rdv = ["BCVs", $bcvs->getDate()->format('d/m/Y'), "", "", ""];
        }
        if ($min_date && $infos && date_diff($min_date, date_create($infos->getDate()->format('y-m-d')), false)->invert) {
            $min_date = date_create($infos->getDate()->format('y-m-d'));
            $rdv = ["Atelier Infos", $infos->getDate()->format('d/m/Y'), "", "", $infos->getType()];
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

            return $this->redirectToRoute('vue', ['id' => $patient->getId()]);
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

        $BCVs = new BCVs();
        $formBCVs = $this->createForm(BCVsType::class, $BCVs);

        $Infos = new Infos();
        $formInfos = $this->createForm(InfosType::class, $Infos);

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
            'formBCVs' => $formBCVs->createView(),
            'formInfos' => $formInfos->createView(),
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
        $data = str_replace("observ;divers;sexe;nom;prenom;date;tel1;tel2;distance;etude;profession;activite;diagnostic;dedate;orientation;etpdecision;progetp;precisions;precisionsperso;dentree;motif;etp;"
        , "Suivi à régulariser;Divers;Sexe;Nom;Prénom;Date de naissance;Téléphone 1;Téléphone 2;Distance d'habitation;Niveau d'étude;Profession;Activité actuelle;Diagnostic médical;Date d'entrée;Orientation;ETP Décision;Type de programme;Précision non inclusion;Précision contenu personnalisé;Date de sortie;Motif d'arrêt de programme;Point final parcours ETP;", $data);

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
