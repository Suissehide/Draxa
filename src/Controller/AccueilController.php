<?php

namespace App\Controller;

use App\Constant\ThematiqueConstants;
use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;
use App\Form\AddPatientType;
use App\Service\EtepeaNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/accueil')]
class AccueilController extends AbstractController
{
    private const CATEGORIES = ['Consultation', 'Entretien', 'Coaching', 'Atelier', 'Educative'];

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/', name: 'accueil', methods: 'GET|POST')]
    public function accueil(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $start = \DateTime::createFromFormat('!d/m/Y', (string) $request->get('date'));
            if (!$start) {
                return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
            }
            $end = (clone $start)->setTime(23, 59, 59);

            $slots = $this->em->getRepository(Slot::class)->findByDate($start, $end);

            $response = [];
            foreach (self::CATEGORIES as $categorie) {
                $rows = [];
                foreach ($slots as $slot) {
                    if ($slot->getCategorie() === $categorie) {
                        $rows[] = $this->slotToArray($slot, true);
                    }
                }
                $response[] = $rows;
            }

            return new JsonResponse($response);
        }

        $form = $this->createForm(AddPatientType::class, new Slot());

        return $this->render('accueil/index.html.twig', [
            'title' => 'Accueil',
            'controller_name' => 'AccueilController',
            'form' => $form->createView(),
            'thematiques' => ThematiqueConstants::lists(),
        ]);
    }

    /**
     * @param bool $typeFallback utilise le type du créneau si le rendez-vous n'en a pas
     */
    private function slotToArray(Slot $slot, bool $typeFallback): array
    {
        $patients = [];
        foreach ($slot->getRendezVous() as $r) {
            $p = $r->getPatient();
            $patients[] = [
                'patientId' => $p->getId(),
                'nom' => $p->getNom(),
                'prenom' => $p->getPrenom(),
                'rendezVousId' => $r->getId(),
                'send' => $r->getSend() === 'Oui' ? 'Oui' : 'Non',
                'notes' => $r->getNotes(),
                'type' => $r->getType() ?? ($typeFallback ? $slot->getType() : null) ?? '',
                'venu' => $r->getEtat() ?: '',
                'theraflow' => $p->getDivers(),
                'progetp' => $p->getProgetp(),
            ];
        }

        $soignant = $slot->getSoignant();

        return [
            'id' => $slot->getId(),
            'categorie' => $slot->getCategorie(),
            'date' => $slot->getDate()?->format('d/m/Y'),
            'horaire' => $slot->getHeureDebut()?->format('H:i') . ' - ' . $slot->getHeureFin()?->format('H:i'),
            'thematique' => $slot->getThematique() ?? '',
            'type' => $slot->getType() ?? '',
            'soignant' => $soignant ? $soignant->getPrenom() . ' ' . $soignant->getNom() : '',
            'location' => $slot->getLocation() ?? '',
            'place' => $slot->getPlace(),
            'patients' => $patients,
        ];
    }

    #[Route(path: '/slot', name: 'slot_list', methods: 'POST')]
    public function slot_list(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false);
        }

        $current = (int) $request->get('current', 1);
        $rowCount = (int) $request->get('rowCount', -1);

        $slotRepository = $this->em->getRepository(Slot::class);
        $qb = $slotRepository->findByFilter(
            (array) $request->get('sort'),
            (string) $request->get('searchPhrase', ''),
            (array) $request->get('categories'),
            $request->get('dateDebut'),
            $request->get('dateFin')
        );
        $count = $slotRepository->countFiltered($qb);

        if ($rowCount > 0) {
            $qb->setFirstResult(max(0, $current - 1) * $rowCount)->setMaxResults($rowCount);
        }

        $rows = [];
        foreach ($qb->getQuery()->getResult() as $slot) {
            $rows[] = $this->slotToArray($slot, false);
        }

        return new JsonResponse([
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $rows,
            'total' => $count,
        ]);
    }

    #[Route(path: '/redirect_vue_patient', name: 'redirect_vue_patient', methods: 'POST')]
    public function redirect_vue_patient(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false);
        }

        $url = $this->generateUrl('patient_vue', ['id' => (int) $request->get('id')]);

        return new JsonResponse($url . '#' . rawurlencode((string) $request->get('anchor')));
    }

    #[Route(path: '/add_patient_slot', name: 'add_patient_slot', methods: 'POST')]
    public function add_patient_slot(Request $request, EtepeaNotifier $etepea): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false);
        }

        $slotId = $request->get('slotId');
        $patientId = $request->get('patientId');
        $thematique = $request->get('thematique');
        $type = $request->get('type');

        $slotRepository = $this->em->getRepository(Slot::class);
        if ($slotRepository->isAlreadyInSlot($slotId, $patientId)) {
            return new JsonResponse(false);
        }

        $slot = $slotRepository->find($slotId);
        $patient = $this->em->getRepository(Patient::class)->find($patientId);
        if (!$slot || !$patient) {
            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $rendezVous = (new RendezVous())
            ->setDate($slot->getDate())
            ->setHeure($slot->getHeureDebut())
            ->setThematique($thematique ?: $slot->getThematique())
            ->setType($type ?: $slot->getType())
            ->setCategorie($slot->getCategorie())
            ->setPatient($patient)
            ->setSlot($slot);

        if ($thematique) {
            $slot->setThematique($thematique);
        }

        $this->em->persist($rendezVous);
        $this->em->flush();
        $etepea->rendezVousCree($rendezVous);

        return new JsonResponse([
            'nom' => $patient->getNom(),
            'prenom' => $patient->getPrenom(),
            'rendezVousId' => $rendezVous->getId(),
            'send' => $rendezVous->getSend() === 'Oui' ? 'Oui' : 'Non',
        ]);
    }

    /**
     * Détache le rendez-vous du créneau (le rendez-vous est conservé sur la fiche patient).
     */
    #[Route(path: '/remove_patient_slot', name: 'remove_patient_slot', methods: 'POST')]
    public function remove_patient_slot(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(false);
        }

        $rendezVous = $this->em->getRepository(RendezVous::class)->find($request->get('rendezVousId'));
        $slot = $this->em->getRepository(Slot::class)->find($request->get('slotId'));
        if (!$rendezVous || !$slot) {
            return new JsonResponse(false, Response::HTTP_NOT_FOUND);
        }

        $slot->removeRendezVous($rendezVous);
        $this->em->flush();

        return new JsonResponse(true);
    }
}
