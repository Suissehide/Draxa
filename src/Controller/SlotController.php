<?php

namespace App\Controller;

use App\Entity\Semaine;
use App\Entity\Slot;
use App\Entity\Soignant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/slot")
 */
class SlotController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @Route("/get/{id}", name="slot_get", methods="GET")
     */
    public function getSlot(SerializerInterface $serializer, int $id): Response
    {
        $slot = $this->em->getRepository(Slot::class)->find($id)
            ?? throw $this->createNotFoundException();

        return new JsonResponse($serializer->serialize($slot, 'json', ['groups' => ['slot']]), Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/add", name="slot_add", methods="POST")
     */
    public function add(Request $request): Response
    {
        $date = \DateTime::createFromFormat('!d/m/Y', (string) $request->get('date'));
        if (!$request->isXmlHttpRequest() || !$date) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $slot = (new Slot())
            ->setDate($date)
            ->setSemaine($this->em->getRepository(Semaine::class)->find((int) $request->get('semaineId')));
        $this->hydrate($slot, $request);

        $this->em->persist($slot);
        $this->em->flush();

        return new JsonResponse($this->slotToArray($slot));
    }

    /**
     * @Route("/edit/{id}", name="slot_edit", methods="PATCH")
     */
    public function edit(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $slot = $this->em->getRepository(Slot::class)->find($id);
        if (!$slot) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->hydrate($slot, $request);
        $this->em->flush();

        return new JsonResponse($this->slotToArray($slot));
    }

    /**
     * @Route("/delete/{id}", name="slot_delete", methods="DELETE")
     */
    public function delete(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $slot = $this->em->getRepository(Slot::class)->find($id);
        if (!$slot) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($slot);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * Crée un créneau de même durée, juste après celui-ci.
     *
     * @Route("/duplicate/{id}", name="slot_duplicate", methods="POST")
     */
    public function duplicate(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $slot = $this->em->getRepository(Slot::class)->find($id);
        if (!$slot) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $heureFin = $slot->getHeureFin();
        $duree = $slot->getHeureDebut()->diff($heureFin);

        $new = clone $slot;
        $new->setHeureDebut(clone $heureFin);
        $new->setHeureFin((clone $heureFin)->add($duree));

        $this->em->persist($new);
        $this->em->flush();

        return new JsonResponse([
            'id' => $new->getId(),
            'debut' => $new->getHeureDebut()->format('H:i'),
            'fin' => $new->getHeureFin()->format('H:i'),
        ]);
    }

    /**
     * @Route("/get_hours", name="slot_get_hours", methods="GET|POST")
     */
    public function slot_get_hours(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $this->em->getRepository(Slot::class)->getHoursOfDate($request->get('type'), $request->get('date'))
        );
    }

    /**
     * @Route("/get_thematique_type", name="slot_get_thematique_type", methods="GET|POST")
     */
    public function slot_get_thematique_type(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            $this->em->getRepository(Slot::class)->getThematiqueTypeOfId($request->get('id'))
        );
    }

    private function hydrate(Slot $slot, Request $request): void
    {
        $place = $request->get('place');
        $soignantId = $request->get('soignant');

        $slot
            ->setHeureDebut(\DateTime::createFromFormat('H:i', (string) $request->get('heureDebut')) ?: null)
            ->setHeureFin(\DateTime::createFromFormat('H:i', (string) $request->get('heureFin')) ?: null)
            ->setCategorie($request->get('categorie'))
            ->setThematique($request->get('thematique'))
            ->setType($request->get('type'))
            ->setLocation($request->get('location'))
            ->setPlace($place === '' || $place === null ? null : (int) $place)
            ->setSoignant($soignantId ? $this->em->getRepository(Soignant::class)->find((int) $soignantId) : null);
    }

    private function slotToArray(Slot $slot): array
    {
        $soignant = $slot->getSoignant();

        return [
            'id' => $slot->getId(),
            'debut' => $slot->getHeureDebut()?->format('H:i'),
            'fin' => $slot->getHeureFin()?->format('H:i'),
            'categorie' => $slot->getCategorie(),
            'thematique' => $slot->getThematique(),
            'type' => $slot->getType(),
            'location' => $slot->getLocation(),
            'soignant' => $soignant ? $soignant->getPrenom() . ' ' . $soignant->getNom() : '',
        ];
    }
}
