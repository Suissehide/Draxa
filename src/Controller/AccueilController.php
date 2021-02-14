<?php

namespace App\Controller;

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

        $slot = new Slot();
        $form = $this->createForm(AddPatientType::class, $slot);

        return $this->render('accueil/index.html.twig', [
            'title' => 'Accueil',
            'controller_name' => 'AccueilController',
            'nbPatients' => count($em->getRepository(Patient::class)->findAll()),
            'nbConsultation' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Consultation'])),
            'nbEntretiens' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Entretien'])),
            'nbAtelier' => count($em->getRepository(RendezVous::class)->findBy(['categorie' => 'Atelier'])),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajax", name="accueil_ajax", methods="GET|POST")
     */
    public function accueil_ajax(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $date = $request->request->get('date');
            $startdatetime = \DateTime::createFromFormat("d/m/Y H:i:s", date($date . " 00:00:00"));
            $enddatetime = \DateTime::createFromFormat("d/m/Y H:i:s", date($date . " 23:59:59"));

            $slots = $em->getRepository(Slot::class)->findByDate($startdatetime, $enddatetime);

            $response = array();
            $response[] = $this->createSlotCategorie($slots, 'Consultation');
            $response[] = $this->createSlotCategorie($slots, 'Entretien');
            $response[] = $this->createSlotCategorie($slots, 'Atelier');

            return new JsonResponse($response, Response::HTTP_OK);
        }
    }

    private function createSlotCategorie(Array $slots, String $categorie)
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
                            'send' =>  $r->getSend() == 'Oui' ? 'Oui' : 'Non'
                        );
                    }
                }
                $jsonContent[] = array(
                    "id" => $s->getId(),
                    "horaire" => $s->getHeureDebut()->format('H:i') . ' - ' . $s->getHeureFin()->format('H:i'),
                    "thematique" => $s->getThematique(),
                    "type" => $s->getType() == null ? "" : $s->getType(),
                    "soignant" => $s->getSoignant() == null ? "" : $s->getSoignant()->getPrenom() . ' ' . $s->getSoignant()->getNom(),
                    "location" => $s->getLocation() == null ? "" : $s->getLocation(),
                    "patients" => empty($rendezVous) ? "" : $patientsArray,
                );
            }
        }
        return $jsonContent;
    }
    
    /**
     * @Route("/vue", name="redirect_vue_patient", methods="GET|POST")
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
     * @Route("/add_patient", name="add_patient_slot", methods="GET|POST")
     */
    public function add_patient_slot(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $slotId = $request->request->get('slotId');
            $patientId = $request->request->get('patientId');

            $slot = $em->getRepository(Slot::class)->findOneById($slotId);
            $patient = $em->getRepository(Patient::class)->findOneById($patientId);

            $rendezVous = new RendezVous();
            $rendezVous->setDate(\DateTime::createFromFormat('d/m/Y', $request->request->get('date')));
            $rendezVous->setHeure(\DateTime::createFromFormat('H:i', $request->request->get('heure')));
            $rendezVous->setCategorie($request->request->get('categorie'));
            $rendezVous->setPatient($patient);
            $rendezVous->setSlot($slot);
            
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
     * @Route("/remove_patient", name="remove_patient_slot", methods="GET|POST")
     */
    public function remove_patient_slot(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $rendezVousId = $request->request->get('rendezVousId');

            $rendezVous = $em->getRepository(RendezVous::class)->findOneById($rendezVousId);
            $em->remove($rendezVous);
            $em->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }
}
