<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Entity\Semaine;
use App\Entity\Soignant;
use App\Entity\Patient;
use App\Entity\RendezVous;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/slot")
 */
class SlotController extends AbstractController
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
     * @Route("/", name="slot_index")
     */
    public function index(): Response
    {
        return $this->render('slot/index.html.twig', [
            'controller_name' => 'SlotController',
        ]);
    }

    /**
     * @Route("/get/{id}", name="slot_get", methods={"GET"})
     */
    public function getSlot(ManagerRegistry $doctrine, int $id): Response
    {
        $slot = $doctrine->getRepository(Slot::class)->find($id);
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($slot, 'json', ['groups' => ['slot']]);
        return new Response($json);
    }

    /**
     * @Route("/add", name="slot_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $date = explode('/', $request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $semaineId = $request->get('semaineId');
            $heureDebut = $request->get('heureDebut');
            $heureFin = $request->get('heureFin');
            $categorie = $request->get('categorie');
            $thematique = $request->get('thematique');
            $type = $request->get('type');
            $location = $request->get('location');
            $place = $request->get('place');
            $soignant = $request->get('soignant');
            $patient = $request->get('patient');

            $semaine = $this->em->getRepository(Semaine::class)->findOneBy(array('id' => $semaineId));

            $slot = new Slot();
            $slot->setDate($new_date);
            $slot->setHeureDebut(date_create_from_format('H:i', $heureDebut));
            $slot->setHeureFin(date_create_from_format('H:i', $heureFin));
            $slot->setCategorie($categorie);
            $slot->setThematique($thematique);
            $slot->setType($type);
            $slot->setLocation($location);
            $slot->setPlace($place === '' ? null : $place);
            $slot->setSoignant($this->em->getRepository(Soignant::class)->findOneById($soignant));
            
            if ($patient) {
                foreach ($patient as $id) {
                    if ($id) {
                        $patient = $this->em->getRepository(Patient::class)->findOneById($id);
                        $rendezVous = new RendezVous();
                        $rendezVous->setDate($new_date);
                        $rendezVous->setHeure(date_create_from_format('H:i', $heureDebut));
                        $rendezVous->setCategorie($categorie);
                        $rendezVous->setPatient($patient);
                        $slot->addRendezVous($rendezVous);
                    }
                }
            }
            $slot->setSemaine($semaine);
            $this->em->persist($slot);
            $this->em->flush();

            return new JsonResponse([
                'id' => $slot->getId(),
                'debut' => date_format($slot->getHeureDebut(), 'H:i'),
                'fin' => date_format($slot->getHeureFin(), 'H:i'),
                'categorie' => $slot->getCategorie(),
                'thematique' => $slot->getThematique(),
                'type' => $slot->getType(),
                'location' => $slot->getLocation(),
                'soignant' => $slot->getSoignant() ? $slot->getSoignant()->getPrenom() . ' ' . $slot->getSoignant()->getNom() : '',
            ]);
        }
    }

    /**
     * @Route("/edit/{id}", name="slot_edit", methods={"PATCH"})
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if ($request->isXmlHttpRequest()) {
            $slot = $doctrine->getRepository(Slot::class)->find($id);
            $heureDebut = $request->get('heureDebut');
            $heureFin = $request->get('heureFin');
            $categorie = $request->get('categorie');
            $thematique = $request->get('thematique');
            $type = $request->get('type');
            $location = $request->get('location');
            $place = $request->get('place');
            $soignant = $request->get('soignant');
            $patient = $request->get('patient');

            $slot->setHeureDebut(date_create_from_format('H:i', $heureDebut));
            $slot->setHeureFin(date_create_from_format('H:i', $heureFin));
            $slot->setCategorie($categorie);
            $slot->setThematique($thematique);
            $slot->setType($type);
            $slot->setLocation($location);
            $slot->setPlace($place === '' ? null : $place);
            $slot->setSoignant($this->em->getRepository(Soignant::class)->findOneById($soignant));

            $rdv = $slot->getRendezVous();
            foreach($rdv as $r) {
                $this->em->remove($r);
            }

            if ($patient) {
                foreach ($patient as $id) {
                    if ($id) {
                        $patient = $this->em->getRepository(Patient::class)->findOneById($id);
                        $rendezVous = new RendezVous();
                        $rendezVous->setDate($slot->getDate());
                        $rendezVous->setHeure(date_create_from_format('H:i', $heureDebut));
                        $rendezVous->setCategorie($categorie);
                        $rendezVous->setThematique($thematique);
                        $rendezVous->setType($type);
                        $rendezVous->setPatient($patient);
                        $slot->addRendezVous($rendezVous);
                    }
                }
            }
            $this->em->flush();

            return new JsonResponse([
                'id' => $slot->getId(),
                'debut' => date_format($slot->getHeureDebut(), 'H:i'),
                'fin' => date_format($slot->getHeureFin(), 'H:i'),
                'categorie' => $slot->getCategorie(),
                'thematique' => $slot->getThematique(),
                'type' => $slot->getType(),
                'location' => $slot->getLocation(),
                'soignant' => $slot->getSoignant() ? $slot->getSoignant()->getPrenom() . ' ' . $slot->getSoignant()->getNom() : '',
            ]);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/delete/{id}", name="slot_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if ($request->isXmlHttpRequest()) {
            $slot = $doctrine->getRepository(Slot::class)->find($id);
            if ($slot) {
                $this->em->remove($slot);
                $this->em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/duplicate/{id}", name="slot_duplicate", methods={"POST"})
     */
    public function duplicate(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if ($request->isXmlHttpRequest()) {
            $slot = $doctrine->getRepository(Slot::class)->find($id);
            if ($slot) {
                $new = clone $slot;
                $heureDebut = $slot->getHeureDebut();
                $heureFin = $slot->getHeureFin();
                $hourDiff = $heureDebut->diff($heureFin);
                $new->setHeureDebut(clone $heureFin);
                $new->setHeureFin($heureFin->add($hourDiff));

                $this->em->persist($new);
                $this->em->flush();
                return new JsonResponse([
                    'id' => $new->getId(),
                    'debut' => date_format($new->getHeureDebut(), 'H:i'),
                    'fin' => date_format($new->getHeureFin(), 'H:i'),
                ]);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/get_hours", name="slot_get_hours", methods="GET|POST")
     */
    public function slot_get_hours(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $date = $request->get('date');
            $type = $request->get('type');
            $hours = $this->em->getRepository(Slot::class)->getHoursOfDate($type, $date);

            return new JsonResponse($hours);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/get_thematique_type", name="slot_get_thematique_type", methods="GET|POST")
     */
    public function slot_get_thematique_type(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $this->em = $this->getDoctrine()->getManager();
            $id = $request->get('id');

            $res = $this->em->getRepository(Slot::class)->getThematiqueTypeOfId($id);

            return new JsonResponse($res);
        }
        return new JsonResponse(false);
    }
}
