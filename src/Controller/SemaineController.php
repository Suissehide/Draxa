<?php

namespace App\Controller;

use App\Entity\Semaine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/semaine")
 */
class SemaineController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @Route("/add", name="semaine_add", methods="POST")
     */
    public function add(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        [$debut, $fin] = $this->parseDates($request);
        if (!$debut || !$fin || $this->existsAt($request->get('dateDebut'))) {
            return new JsonResponse(false);
        }

        $semaine = (new Semaine())->setDateDebut($debut)->setDateFin($fin);
        $this->em->persist($semaine);
        $this->em->flush();

        return new JsonResponse($semaine->getId());
    }

    /**
     * @Route("/same_date", name="semaine_sameDate", methods="POST")
     */
    public function semaine_isSameDate(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(!$this->existsAt($request->get('dateDebut')));
    }

    /**
     * @Route("/{id}", name="semaine_delete", methods="DELETE")
     */
    public function delete(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $semaine = $this->em->getRepository(Semaine::class)->find($id);
        if (!$semaine) {
            return new JsonResponse(false);
        }

        $this->em->remove($semaine);
        $this->em->flush();

        return new JsonResponse(true);
    }

    /**
     * Copie une semaine et tous ses créneaux (sans les rendez-vous) à une nouvelle date.
     *
     * @Route("/duplicate/{id}", name="semaine_duplicate", methods="POST")
     */
    public function duplicate(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $semaine = $this->em->getRepository(Semaine::class)->find($id);
        [$debut, $fin] = $this->parseDates($request);
        if (!$semaine || !$debut || !$fin || $this->existsAt($request->get('dateDebut'))) {
            return new JsonResponse(false);
        }

        $newSemaine = (clone $semaine)->setDateDebut($debut)->setDateFin($fin);
        foreach ($semaine->getSlots() as $slot) {
            $offset = $slot->getDate()->diff($semaine->getDateDebut());
            $newSlot = clone $slot;
            $newSlot->setDate((clone $debut)->sub($offset));
            $newSemaine->addSlot($newSlot);
        }
        $this->em->persist($newSemaine);
        $this->em->flush();

        $slotsJson = [];
        foreach ($newSemaine->getSlots() as $newSlot) {
            $soignant = $newSlot->getSoignant();
            $slotsJson[] = [
                'id' => $newSlot->getId(),
                'date' => $newSlot->getDate()->format('d/m/Y'),
                'debut' => $newSlot->getHeureDebut()?->format('H:i'),
                'fin' => $newSlot->getHeureFin()?->format('H:i'),
                'categorie' => $newSlot->getCategorie(),
                'thematique' => $newSlot->getThematique(),
                'type' => $newSlot->getType(),
                'location' => $newSlot->getLocation(),
                'soignant' => $soignant ? $soignant->getPrenom() . ' ' . $soignant->getNom() : '',
            ];
        }

        return new JsonResponse([
            'id' => $newSemaine->getId(),
            'debut' => $newSemaine->getDateDebut()->format('d/m/Y'),
            'fin' => $newSemaine->getDateFin()->format('d/m/Y'),
            'slots' => $slotsJson,
        ]);
    }

    /**
     * @return array{0: \DateTime|false, 1: \DateTime|false}
     */
    private function parseDates(Request $request): array
    {
        return [
            \DateTime::createFromFormat('!d/m/Y', (string) $request->get('dateDebut')),
            \DateTime::createFromFormat('!d/m/Y', (string) $request->get('dateFin')),
        ];
    }

    private function existsAt(?string $dateDebut): bool
    {
        return $this->em->getRepository(Semaine::class)->findAtSameDate($dateDebut) !== null;
    }
}
