<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;

use App\Repository\PatientRepository;

use App\Form\AddPatientType;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/accueil")
 */
class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil", methods="GET|POST")
     */
    public function accueil(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
            $date = $request->request->get('date');
            $startdatetime = \DateTime::createFromFormat("d/m/Y H:i:s", date($date . " 00:00:00"));
            $enddatetime = \DateTime::createFromFormat("d/m/Y H:i:s", date($date . " 23:59:59"));

            $slots = $em->getRepository(Slot::class)->findByDate($startdatetime, $enddatetime);

            $response = array();
            $response[] = $this->createSlotCategorie($slots, 'Consultation');
            $response[] = $this->createSlotCategorie($slots, 'Entretien');
            $response[] = $this->createSlotCategorie($slots, 'Coaching');
            $response[] = $this->createSlotCategorie($slots, 'Atelier');
            $response[] = $this->createSlotCategorie($slots, 'Educative');

            return new JsonResponse($response, Response::HTTP_OK);
        }

        $slot = new Slot();
        $form = $this->createForm(AddPatientType::class, $slot);

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

        return $this->render('accueil/index.html.twig', [
            'title' => 'Accueil',
            'controller_name' => 'AccueilController',
            'nbPatients' => count($em->getRepository(Patient::class)->findAll()),
            'nbConsultation' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Consultation'])),
            'nbEntretien' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Entretien'])),
            'nbAtelier' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Atelier'])),
            'nbEducative' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Educative'])),
            'nbCoaching' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Coaching'])),
            'form' => $form->createView(),

            'thematiques' => $thematiques
        ]);
    }

    private function createSlotCategorie(array $slots, String $categorie)
    {
        $jsonContent = array();
        foreach ($slots as $s) {
            if ($s->getCategorie() === $categorie) {
                $patientsArray = array();
                $rendezVous = $s->getRendezVous();
                if (is_array($rendezVous) || is_object($rendezVous)) {
                    foreach ($rendezVous as $r) {
                        $p = $r->getPatient();
                        $patientsArray[] = array(
                            'patientId' => $p->getId(),
                            'nom' => $p->getNom(),
                            'prenom' => $p->getPrenom(),
                            'rendezVousId' => $r->getId(),
                            'send' =>  $r->getSend() == 'Oui' ? 'Oui' : 'Non',
                            'notes' => $r->getNotes(),
                            'type' => $r->getType() == null ? "" : $r->getType(),
                            'venu' => $r->getEtat() ? $r->getEtat() : "",
                            'theraflow' => $p->getDivers(),
                            'progetp' => $p->getProgetp()
                        );
                    }
                }
                $jsonContent[] = array(
                    "id" => $s->getId(),
                    "horaire" => $s->getHeureDebut()->format('H:i') . ' - ' . $s->getHeureFin()->format('H:i'),
                    "thematique" => $s->getThematique() == null ? "" : $s->getThematique(),
                    "type" => $s->getType() == null ? "" : $s->getType(),
                    "soignant" => $s->getSoignant() == null ? "" : $s->getSoignant()->getPrenom() . ' ' . $s->getSoignant()->getNom(),
                    "location" => $s->getLocation() == null ? "" : $s->getLocation(),
                    "place" => $s->getPlace(),
                    "patients" => empty($rendezVous) ? "" : $patientsArray,
                );
            }
        }
        return $jsonContent;
    }

    /**
     * @Route("/slot", name="slot_list", methods="GET|POST")
     */

    public function slot_list(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $categories = $request->request->get('categories');
            $dateDebut = $request->request->get('dateDebut');
            $dateFin = $request->request->get('dateFin');

            $current = $request->request->get('current');
            $rowCount = $request->request->get('rowCount');
            $searchPhrase = $request->request->get('searchPhrase');
            $sort = $request->request->get('sort');

            $em = $this->getDoctrine()->getManager();
            $slotRepository = $em->getRepository(Slot::class);
            $slots = $slotRepository->findByFilter($sort, $searchPhrase, $categories, $dateDebut, $dateFin);
            if ($searchPhrase != "" || $sort != "all") {
                $count = count($slots->getQuery()->getResult());
            } else {
                $count = $slotRepository->compte();
            }
            if ($rowCount != -1) {
                $min = ($current - 1) * $rowCount;
                $max = $rowCount;
                $slots->setMaxResults($max)->setFirstResult($min);
            }

            $jsonContent = [];
            $slots = $slots->getQuery()->getResult();
            foreach ($slots as $s) {
                $patientsArray = array();
                $rendezVous = $s->getRendezVous();
                if (is_array($rendezVous) || is_object($rendezVous)) {
                    foreach ($rendezVous as $r) {
                        $p = $r->getPatient();
                        $patientsArray[] = array(
                            'patientId' => $p->getId(),
                            'nom' => $p->getNom(),
                            'prenom' => $p->getPrenom(),
                            'rendezVousId' => $r->getId(),
                            'send' =>  $r->getSend() == 'Oui' ? 'Oui' : 'Non',
                            'notes' => $r->getNotes(),
                            'type' => $r->getType() == null ? "" : $r->getType(),
                            'venu' => $r->getEtat() ? $r->getEtat() : "",
                            'theraflow' => $p->getDivers(),
                            'progetp' => $p->getProgetp()
                        );
                    }
                }
                $jsonContent[] = array(
                    "id" => $s->getId(),
                    "categorie" => $s->getCategorie(),
                    "date" => $s->getDate()->format('d/m/Y'),
                    "horaire" => $s->getHeureDebut()->format('H:i') . ' - ' . $s->getHeureFin()->format('H:i'),
                    "thematique" => $s->getThematique(),
                    "type" => $s->getType() == null ? "" : $s->getType(),
                    "location" => $s->getLocation() == null ? "" : $s->getLocation(),
                    "soignant" => $s->getSoignant() == null ? "" : $s->getSoignant()->getPrenom() . ' ' . $s->getSoignant()->getNom(),
                    "place" => $s->getPlace(),
                    "patients" => empty($rendezVous) ? "" : $patientsArray,
                );
            }

            $data = array(
                "current" => intval($current),
                "rowCount" => intval($rowCount),
                "rows" => $jsonContent,
                "total" => intval($count)
            );
            return new JsonResponse($data);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/redirect_vue_patient", name="redirect_vue_patient", methods="GET|POST")
     */
    public function redirect_vue_patient(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $id = $request->request->get('id');
            $anchor = $request->request->get('anchor');

            return new JsonResponse($this->generateUrl('patient_vue', array('id' => $id)) . '#' . $anchor);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/add_patient_slot", name="add_patient_slot", methods="GET|POST")
     */
    public function add_patient_slot(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $slotId = $request->request->get('slotId');
            $patientId = $request->request->get('patientId');
            $thematique = $request->request->get('thematique');
            $type = $request->request->get('type');

            $slotRepository = $em->getRepository(Slot::class);
            if ($slotRepository->isAlreadyInSlot($slotId, $patientId))
                return new JsonResponse(false);

            $slot = $slotRepository->findOneById($slotId);
            $patient = $em->getRepository(Patient::class)->findOneById($patientId);

            $rendezVous = new RendezVous();
            $rendezVous->setDate($slot->getDate());
            $rendezVous->setHeure($slot->getHeureDebut());
            $rendezVous->setThematique($thematique ? $thematique : $slot->getThematique());
            $rendezVous->setType($type ? $type : $slot->getType());
            $rendezVous->setCategorie($slot->getCategorie());
            $rendezVous->setPatient($patient);
            $rendezVous->setSlot($slot);

            $slot->setThematique($thematique ? $thematique : $slot->getThematique());

            $em->persist($rendezVous);
            $em->flush();

            return new JsonResponse(
                array(
                    'nom' => $patient->getNom(),
                    'prenom' => $patient->getPrenom(),
                    'rendezVousId' => $rendezVous->getId(),
                    'send' =>  $rendezVous->getSend() == 'Oui' ? 'Oui' : 'Non'
                )
            );
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/remove_patient_slot", name="remove_patient_slot", methods="GET|POST")
     */
    public function remove_patient_slot(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $rendezVousId = $request->request->get('rendezVousId');
            $slotId = $request->request->get('slotId');

            $rendezVous = $em->getRepository(RendezVous::class)->findOneById($rendezVousId);
            $slot = $em->getRepository(Slot::class)->findOneById($slotId);
            
            $slot->removeRendezVous($rendezVous);
            // $em->remove($rendezVous);
            
            if ($slot) {
                if (count($slot->getRendezVous()) == 0) {
                    $slot->setThematique('');
                }
            }

            $em->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }
}
