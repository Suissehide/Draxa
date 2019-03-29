<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rendez/vous")
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
            $em->remove($rendezVous);
            $em->flush();
        }

        return $this->redirectToRoute('rendez_vous_index');
    }

    /**
     * @Route("/add", name="rendezVous_add", methods="GET|POST")
     */
    public function rendezVous_add(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $new_time = new \DateTime();
            if ($request->request->get('time')) {
                $time = explode(':', $request->request->get('time'));
                $new_time->setTime($time[0], $time[1]);
            }
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $rendezVous = new RendezVous();
            $rendezVous->setDate($new_date);
            $rendezVous->setThematique($request->request->get('thematique'));
            $rendezVous->setHeure($new_time);
            $rendezVous->setType($request->request->get('type'));
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
     * @Route("/date_error", name="rendezVous_date_error", methods="GET|POST")
     */
    public function rendezVous_date_error(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $bool = false;
            $id = $request->request->get('id');
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            $now = date_create(date('y-m-d'));

            $em = $this->getDoctrine()->getManager();
            $rendezVous = $em->getRepository(Patient::class)->find($id)->getRendezVous();

            if (date_diff($now, $new_date, false)->invert) {
                $bool = true;
            }
            foreach ($rendezVous as $rd) {
                $date = date_create($rd->getDate()->format('y-m-d'));
                if (!date_diff($new_date, $date, false)->invert) {
                    $bool = true;
                }
            }
            return new JsonResponse($bool);
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
                $new_time = new \DateTime();
                if ($request->request->get('time')) {
                    $time = explode(':', $request->request->get('time'));
                    $new_time->setTime($time[0], $time[1]);
                }
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
                
                $rendezVous->setDate($new_date);
                $rendezVous->setThematique($request->request->get('thematique'));
                $rendezVous->setHeure($new_time);
                $rendezVous->setType($request->request->get('type'));
                $rendezVous->setAccompagnant($request->request->get('accompagnant'));
                $rendezVous->setEtat($request->request->get('etat'));
                $rendezVous->setMotifRefus($request->request->get('motifRefus'));

                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }
}
