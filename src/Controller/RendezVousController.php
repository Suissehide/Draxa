<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;

use App\Form\RendezVousType;

use App\Repository\RendezVousRepository;

use Doctrine\Persistence\ManagerRegistry;
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
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/", name="rendez_vous_index", methods="GET")
     */
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        return $this->render('rendez_vous/index.html.twig', ['rendez_vouses' => $rendezVousRepository->findAll()]);
    }

    /**
     * @Route("/add", name="rendezVous_add", methods="GET|POST")
     */
    public function rendezVous_add(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $slotId = $request->get('slotId');
            $categorie = $request->get('categorie');

            if ($slotId == '')
                return new JsonResponse(false);
            $slot = $this->em->getRepository(Slot::class)->find($slotId);
            if (!$slot)
                return new JsonResponse(false);

            $date = explode('/', $request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            // $patientId = $request->get('patientId');
            // if ($this->em->getRepository(RendezVous::class)->findSameDate($request->get('date'), $patientId, $categorie) != [])
            //     return new JsonResponse(false);

            // $slot->setThematique($request->get('thematique'));
            // $slot->setType($request->get('type'));
            $rendezVous = new RendezVous();
            $rendezVous->setCategorie($categorie);
            $rendezVous->setSlot($slot);
            $rendezVous->setDate($new_date);
            $rendezVous->setHeure(\DateTime::createFromFormat('H:i', $request->get('time')));
            $rendezVous->setThematique($request->get('thematique'));
            $rendezVous->setType($request->get('type'));
            $rendezVous->setAccompagnant($request->get('accompagnant'));
            $rendezVous->setEtat($request->get('etat'));
            $rendezVous->setMotifRefus($request->get('motifRefus'));
            $rendezVous->setNotes($request->get('notes'));
            $rendezVous->setPatient($this->em->getRepository(Patient::class)->findOneById($id));

            $this->em->persist($rendezVous);
            $this->em->flush();
            return new JsonResponse($rendezVous->getId());
        }
    }

    /**
     * @Route("/remove/{id}", name="rendezVous_remove", methods="DELETE")
     */
    public function rendezVous_remove(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $rendezVous = $doctrine->getRepository(RendezVous::class)->find($id);
            if ($rendezVous) {
                $slot = $rendezVous->getSlot();
                $slot->removeRendezVous($rendezVous);
                $this->em->remove($rendezVous);

                if ($slot) {
                    if (count($slot->getRendezVous()) == 0) {
                        $slot->setThematique('');
                    }
                }
                $this->em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/patch/{id}", name="rendezVous_patch", methods="GET|POST")
     */
    public function rendezVous_patch(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        if ($request->isXmlHttpRequest()) {
            $rendezVous = $doctrine->getRepository(RendezVous::class)->find($id);
            if ($rendezVous) {
                $slotId = $request->get('slotId');

                $date = explode('/', $request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

                $patientId = $request->get('patientId');
                $t = $this->em->getRepository(RendezVous::class)->findSameDate($request->get('date'), $patientId, $rendezVous->getCategorie());
                if ($t != [] && $t[0]->getDate() != $rendezVous->getDate()) {
                    return new Response(null, 500);
                }

                $rendezVous->setDate($new_date);
                $rendezVous->setHeure(\DateTime::createFromFormat('H:i', $request->get('time')));
                $rendezVous->setThematique($request->get('thematique'));
                $rendezVous->setType($request->get('type'));
                $rendezVous->setAccompagnant($request->get('accompagnant'));
                $rendezVous->setEtat($request->get('etat'));
                $rendezVous->setMotifRefus($request->get('motifRefus'));
                $rendezVous->setNotes($request->get('notes'));

                if ($slotId !== '') {
                    $slot = $this->em->getRepository(Slot::class)->find($slotId);
                    if ($slot) {
                        $slot->setThematique($request->get('thematique'));
                        // $slot->setType($request->get('type'));
                        $rendezVous->setSlot($slot);
                    }
                }

                $this->em->flush();
                return new Response(null, 204);
            }
            return new Response(null, 500);
        }
        return new Response(null, 500);
    }

    /**
     * @Route("/send", name="rendezVous_send", methods="POST")
     */

    public function rendezVous_send(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $send = $request->get('send');

            $rendezVous = $this->em->getRepository(RendezVous::class)->find($id);
            $rendezVous->setSend($send);
            $this->em->flush();

            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }



    /**
     * OLD ROUTE
     */

    /**
     * @Route("/new", name="rendez_vous_new", methods="GET|POST")
     */
    function new (Request $request): Response {
        $rendezVous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($rendezVous);
            $this->em->flush();

            return $this->redirectToRoute('rendez_vous_index');
        }

        return $this->render('rendez_vous/new.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rendez_vous_show", methods="GET")
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $rendezVous = $doctrine->getRepository(RendezVous::class)->find($id);
        return $this->render('rendez_vous/show.html.twig', ['rendez_vous' => $rendezVous]);
    }

    /**
     * @Route("/{id}/edit", name="rendez_vous_edit", methods="GET|POST")
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $rendezVous = $doctrine->getRepository(RendezVous::class)->find($id);
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('rendez_vous_edit', ['id' => $rendezVous->getId()]);
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rendez_vous_delete", methods="DELETE")
     */
    public function delete(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $rendezVous = $doctrine->getRepository(RendezVous::class)->find($id);
        if ($this->isCsrfTokenValid('delete' . $rendezVous->getId(), $request->get('_token'))) {
            $rendezVous->setSlot(null);
            $this->em->remove($rendezVous);
            $this->em->flush();
        }

        return $this->redirectToRoute('rendez_vous_index');
    }
}
