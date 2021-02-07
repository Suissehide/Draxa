<?php

namespace App\Controller;

use App\Entity\Semaine;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/semaine")
 */
class SemaineController extends AbstractController
{
    /**
     * @Route("/add", name="semaine_add", methods="POST")
     */
    public function add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($em->getRepository(Semaine::class)->findSemaineAtSameDate($request->request->get('dateDebut')) != [])
                return new JsonResponse(false);

            $dateDebut = explode('/', $request->request->get('dateDebut'));
            $new_dateDebut = date_create(date("y-m-d", mktime(0, 0, 0, $dateDebut[1], $dateDebut[0], $dateDebut[2])));

            $dateFin = explode('/', $request->request->get('dateFin'));
            $new_dateFin = date_create(date("y-m-d", mktime(0, 0, 0, $dateFin[1], $dateFin[0], $dateFin[2])));

            $semaine = new Semaine();
            $semaine->setDateDebut($new_dateDebut);
            $semaine->setDateFin($new_dateFin);
            $em->persist($semaine);
            $em->flush();

            return new JsonResponse($semaine->getId());
        }
    }

    /**
     * @Route("/date", name="semaine_date", methods="POST")
     */
    public function semaine_date(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $isSemaine = $em->getRepository(Semaine::class)->findSemaineAtSameDate($request->request->get('dateDebut'));

            return new JsonResponse($isSemaine ? false : true);
        }
    }

    /**
     * @Route("/{id}", name="semaine_delete", methods="DELETE")
     */
    public function delete(Request $request, semaine $semaine) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($semaine) {
                $em->remove($semaine);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/duplicate/{id}", name="semaine_duplicate", methods="POST")
     */
    public function duplicate(Request $request, Semaine $semaine): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($semaine) {
                if ($em->getRepository(Semaine::class)->findSemaineAtSameDate($request->request->get('dateDebut')) != [])
                    return new JsonResponse(false);

                $new = clone $semaine;

                $dateDebut = explode('/', $request->request->get('dateDebut'));
                $new_dateDebut = date_create(date("y-m-d", mktime(0, 0, 0, $dateDebut[1], $dateDebut[0], $dateDebut[2])));
                $dateFin = explode('/', $request->request->get('dateFin'));
                $new_dateFin = date_create(date("y-m-d", mktime(0, 0, 0, $dateFin[1], $dateFin[0], $dateFin[2])));

                $new->setDateDebut($new_dateDebut);
                $new->setDateFin($new_dateFin);

                $slots = $semaine->getSlots();
                $slotsJson = [];
                foreach ($slots as $slot) {
                    $diff = date_diff($slot->getDate(), $semaine->getDateDebut());
                    $newSlot = clone $slot;
                    $d = clone $new_dateDebut;
                    $newSlot->setDate(date_sub($d, $diff));
                    $new->addSlot($newSlot);
                    $slotsJson[] = array(
                        'id' => $newSlot->getId(),
                        'date' => $newSlot->getDate()->format('d/m/Y'),
                        'debut' => date_format($newSlot->getHeureDebut(), 'H:i'),
                        'fin' => date_format($newSlot->getHeureFin(), 'H:i'),
                        'categorie' => $newSlot->getCategorie(),
                        'thematique' => $newSlot->getThematique(),
                        'type' => $newSlot->getType(),
                        'location' => $newSlot->getLocation(),
                        'soignant' => $newSlot->getSoignant() ? $newSlot->getSoignant()->getPrenom() . ' ' . $newSlot->getSoignant()->getNom() : '',
                    );
                }

                $em->persist($new);
                $em->flush();
                return new JsonResponse([
                    'id' => $new->getId(),
                    'debut' => $new->getDateDebut()->format('d/m/Y'),
                    'fin' => $new->getDateFin()->format('d/m/Y'),
                    'slots' => $slotsJson
                ]);
            }
            return new JsonResponse(false);
        }
    }
}
