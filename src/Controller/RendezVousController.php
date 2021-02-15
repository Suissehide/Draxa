<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Slot;

use App\Form\RendezVousType;

use App\Repository\RendezVousRepository;

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
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');
            $slotId = $request->request->get('slotId');
            $categorie = $request->request->get('categorie');

            if ($slotId == '')
                return new JsonResponse(false);
            $slot = $em->getRepository(Slot::class)->find($slotId);
            if (!$slot)
                return new JsonResponse(false);

            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $patientId = $request->request->get('patientId');
            if ($em->getRepository(RendezVous::class)->findSameDate($request->request->get('date'), $patientId, $categorie) != [])
                return new JsonResponse(false);

            $slot->setThematique($request->request->get('thematique'));
            $rendezVous = new RendezVous();
            $rendezVous->setCategorie($categorie);
            $rendezVous->setSlot($slot);
            $rendezVous->setDate($new_date);
            $rendezVous->setHeure(\DateTime::createFromFormat('H:i', $request->request->get('time')));
            $rendezVous->setAccompagnant($request->request->get('accompagnant'));
            $rendezVous->setEtat($request->request->get('etat'));
            $rendezVous->setMotifRefus($request->request->get('motifRefus'));
            $rendezVous->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($rendezVous);
            $em->flush();
            return new JsonResponse($rendezVous->getId());
        }
    }

    /**
     * @Route("/remove/{id}", name="rendezVous_remove", methods="DELETE")
     */
    public function rendezVous_remove(Request $request, RendezVous $rendezVous): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($rendezVous) {
                $em->remove($rendezVous);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/patch/{id}", name="rendezVous_patch", methods="GET|POST")
     */
    public function rendezVous_patch(Request $request, RendezVous $rendezVous): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($rendezVous) {
                $slotId = $request->request->get('slotId');

                if ($slotId == '')
                    return new JsonResponse(false);
                $slot = $em->getRepository(Slot::class)->find($slotId);
                if (!$slot)
                    return new JsonResponse(false);

                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

                $patientId = $request->request->get('patientId');
                $t = $em->getRepository(RendezVous::class)->findSameDate($request->request->get('date'), $patientId, $rendezVous->getCategorie());
                if ($t != [] && $t[0]->getDate() != $rendezVous->getDate())
                    return new JsonResponse(false);

                $slot->setThematique($request->request->get('thematique'));
                $rendezVous->setSlot($slot);
                $rendezVous->setDate($new_date);
                $rendezVous->setHeure(\DateTime::createFromFormat('H:i', $request->request->get('time')));
                $rendezVous->setAccompagnant($request->request->get('accompagnant'));
                $rendezVous->setEtat($request->request->get('etat'));
                $rendezVous->setMotifRefus($request->request->get('motifRefus'));

                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/send", name="rendezVous_send", methods="POST")
     */

    public function rendezVous_send(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $id = $request->request->get('id');
            $send = $request->request->get('send');

            $rendezVous = $em->getRepository(RendezVous::class)->find($id);
            $rendezVous->setSend($send);
            $em->flush();

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($rendezVous);
            $em->flush();

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
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('rendez_vous/show.html.twig', ['rendez_vous' => $rendezVous]);
    }

    /**
     * @Route("/{id}/edit", name="rendez_vous_edit", methods="GET|POST")
     */
    public function edit(Request $request, RendezVous $rendezVous): Response
    {
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
    public function delete(Request $request, RendezVous $rendezVous): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rendezVous->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $rendezVous->setSlot();
            $em->remove($rendezVous);
            $em->flush();
        }

        return $this->redirectToRoute('rendez_vous_index');
    }
}
