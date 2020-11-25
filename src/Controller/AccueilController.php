<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Entretien;
use App\Entity\Atelier;
use App\Entity\RendezVous;

use App\Repository\PatientRepository;
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

        return $this->render('accueil/index.html.twig', [
            'title' => 'Accueil',
            'date' => date("d-m-Y"),
            'nbPatients' => count($em->getRepository(Patient::class)->findAll()),
            'nbEntretiens' => count($em->getRepository(Entretien::class)->findAll()),
            'controller_name' => 'AccueilController',
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
            $startdatetime = \DateTime::createFromFormat("Y-m-d H:i:s", date($date . " 00:00:00"));
            $enddatetime = \DateTime::createFromFormat("Y-m-d H:i:s", date($date . " 23:59:59"));

            $entretiens = $em->getRepository(Entretien::class)->findOneByDateJoinedToEntretien($startdatetime, $enddatetime);
            $ateliers = $em->getRepository(Atelier::class)->findOneByDateJoinedToAtelier($startdatetime, $enddatetime);
            $rendezVous = $em->getRepository(RendezVous::class)->findOneByDateJoinedToRendezVous($startdatetime, $enddatetime);

            $jsonContent = array();
            $response = array();
            foreach ($entretiens as $e) {
                $jsonContent[] = array(
                    "id" => $e->getPatient()->getId(),
                    "nom" => $e->getPatient()->getNom(),
                    "prenom" => $e->getPatient()->getPrenom(),
                    "type" => $e->getType() == null ? "" : $e->getType(),
                    "heure" => $e->getHeure() == null ? "" : $e->getHeure()->format('H:i'),
                    "thematique" => $e->getThematique(),
                );
            }
            $response[] = $jsonContent;
            $jsonContent = array();
            foreach ($ateliers as $e) {
                $jsonContent[] = array(
                    "id" => $e->getPatient()->getId(),
                    "nom" => $e->getPatient()->getNom(),
                    "prenom" => $e->getPatient()->getPrenom(),
                    "type" => $e->getType() == null ? "" : $e->getType(),
                    "heure" => $e->getHeure() == null ? "" : $e->getHeure()->format('H:i'),
                    "thematique" => $e->getThematique(),
                );
            }
            $response[] = $jsonContent;
            $jsonContent = array();
            foreach ($rendezVous as $e) {
                $jsonContent[] = array(
                    "id" => $e->getPatient()->getId(),
                    "nom" => $e->getPatient()->getNom(),
                    "prenom" => $e->getPatient()->getPrenom(),
                    "type" => $e->getType() == null ? "" : $e->getType(),
                    "heure" => $e->getHeure() == null ? "" : $e->getHeure()->format('H:i'),
                    "thematique" => $e->getThematique(),
                );
            }
            $response[] = $jsonContent;

            return new JsonResponse($response, Response::HTTP_OK);
        }
    }
}
