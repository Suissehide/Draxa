<?php

namespace App\Controller;

use App\Entity\Semaine;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/add", name="semaine_add", methods="POST")
     */
    public function add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            if ($this->em->getRepository(Semaine::class)->findSemaineAtSameDate($request->get('dateDebut')) != [])
                return new JsonResponse(false);

            $dateDebut = explode('/', $request->get('dateDebut'));
            $new_dateDebut = date_create(date("y-m-d", mktime(0, 0, 0, $dateDebut[1], $dateDebut[0], $dateDebut[2])));

            $dateFin = explode('/', $request->get('dateFin'));
            $new_dateFin = date_create(date("y-m-d", mktime(0, 0, 0, $dateFin[1], $dateFin[0], $dateFin[2])));

            $semaine = new Semaine();
            $semaine->setDateDebut($new_dateDebut);
            $semaine->setDateFin($new_dateFin);
            $this->em->persist($semaine);
            $this->em->flush();

            return new JsonResponse($semaine->getId());
        }
    }

    /**
     * @Route("/date", name="semaine_date", methods="POST")
     */
    public function semaine_date(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $isSemaine = $this->em->getRepository(Semaine::class)->findSemaineAtSameDate($request->get('dateDebut'));

            return new JsonResponse($isSemaine ? false : true);
        }
    }

    /**
     * @Route("/{id}", name="semaine_delete", methods="DELETE")
     */
    public function delete(Request $request, ManagerRegistry $doctrine, int $id) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $semaine = $doctrine->getRepository(Semaine::class)->find($id);
            if ($semaine) {
                $this->em->remove($semaine);
                $this->em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/duplicate/{id}", name="semaine_duplicate", methods="POST")
     */
    public function duplicate(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if ($request->isXmlHttpRequest()) {
            $semaine = $doctrine->getRepository(Semaine::class)->find($id);
            if ($semaine) {
                if ($this->em->getRepository(Semaine::class)->findSemaineAtSameDate($request->get('dateDebut')) != [])
                    return new JsonResponse(false);

                $newSemaine = clone $semaine;

                $dateDebut = explode('/', $request->get('dateDebut'));
                $new_dateDebut = date_create(date("y-m-d", mktime(0, 0, 0, $dateDebut[1], $dateDebut[0], $dateDebut[2])));
                $dateFin = explode('/', $request->get('dateFin'));
                $new_dateFin = date_create(date("y-m-d", mktime(0, 0, 0, $dateFin[1], $dateFin[0], $dateFin[2])));

                $newSemaine->setDateDebut($new_dateDebut);
                $newSemaine->setDateFin($new_dateFin);

                $slots = $semaine->getSlots();
                $slotsJson = [];
                foreach ($slots as $slot) {
                    $diff = date_diff($slot->getDate(), $semaine->getDateDebut());
                    $newSlot = clone $slot;
                    $d = clone $new_dateDebut;
                    $newSlot->setDate(date_sub($d, $diff));
                    $newSemaine->addSlot($newSlot);

                    $this->em->persist($newSemaine);
                    $this->em->flush();

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
                return new JsonResponse([
                    'id' => $newSemaine->getId(),
                    'debut' => $newSemaine->getDateDebut()->format('d/m/Y'),
                    'fin' => $newSemaine->getDateFin()->format('d/m/Y'),
                    'slots' => $slotsJson
                ]);
            }
            return new JsonResponse(false);
        }
    }
}
