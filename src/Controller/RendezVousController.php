<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rendez_vous")
 */
class RendezVousController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @Route("/add", name="rendezVous_add", methods="POST")
     */
    public function rendezVous_add(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $slot = $this->em->getRepository(Slot::class)->find((int) $request->get('slotId'));
        $patient = $this->em->getRepository(Patient::class)->find((int) $request->get('id'));
        $date = \DateTime::createFromFormat('!d/m/Y', (string) $request->get('date'));
        if (!$slot || !$patient || !$date) {
            return new JsonResponse(false);
        }

        $rendezVous = (new RendezVous())
            ->setCategorie($request->get('categorie'))
            ->setSlot($slot)
            ->setPatient($patient);
        $this->hydrate($rendezVous, $request, $date);

        $this->em->persist($rendezVous);
        $this->em->flush();

        return new JsonResponse($rendezVous->getId());
    }

    /**
     * @Route("/remove/{id}", name="rendezVous_remove", methods="DELETE")
     */
    public function rendezVous_remove(Request $request, int $id): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $rendezVous = $this->em->getRepository(RendezVous::class)->find($id);
        if (!$rendezVous) {
            return new JsonResponse(false);
        }

        $slot = $rendezVous->getSlot();
        if ($slot) {
            $slot->removeRendezVous($rendezVous);
            if ($slot->getRendezVous()->isEmpty()) {
                $slot->setThematique('');
            }
        }
        $this->em->remove($rendezVous);
        $this->em->flush();

        return new JsonResponse(true);
    }

    /**
     * @Route("/patch/{id}", name="rendezVous_patch", methods="POST")
     */
    public function rendezVous_patch(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $rendezVous = $this->em->getRepository(RendezVous::class)->find($id);
        $date = \DateTime::createFromFormat('!d/m/Y', (string) $request->get('date'));
        if (!$rendezVous || !$date) {
            return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Refuse un second rendez-vous de même catégorie le même jour pour ce patient.
        $sameDay = $this->em->getRepository(RendezVous::class)
            ->findSameDate($request->get('date'), $request->get('patientId'), $rendezVous->getCategorie());
        if ($sameDay && $sameDay[0]->getDate() != $rendezVous->getDate()) {
            return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->hydrate($rendezVous, $request, $date);

        $slotId = $request->get('slotId');
        if ($slotId && $slot = $this->em->getRepository(Slot::class)->find((int) $slotId)) {
            $slot->setThematique($request->get('thematique'));
            $rendezVous->setSlot($slot);
        }

        $this->em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/send", name="rendezVous_send", methods="POST")
     */
    public function rendezVous_send(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false);
        }

        $rendezVous = $this->em->getRepository(RendezVous::class)->find((int) $request->get('id'));
        if (!$rendezVous) {
            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $rendezVous->setSend($request->get('send'));
        $this->em->flush();

        return new JsonResponse(true);
    }

    private function hydrate(RendezVous $rendezVous, Request $request, \DateTime $date): void
    {
        $heure = \DateTime::createFromFormat('H:i', (string) $request->get('time'));

        $rendezVous
            ->setDate($date)
            ->setHeure($heure ?: null)
            ->setThematique($request->get('thematique'))
            ->setType($request->get('type'))
            ->setAccompagnant($request->get('accompagnant'))
            ->setEtat($request->get('etat'))
            ->setMotifRefus($request->get('motifRefus'))
            ->setNotes($request->get('notes'));
    }
}
