<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\BCVs;
use App\Form\BCVsType;
use App\Repository\BCVsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/bcvs")
 */
class BCVsController extends AbstractController
{
    /**
     * @Route("/", name="bcvs_index", methods={"GET"})
     */
    public function index(BCVsRepository $bCVsRepository): Response
    {
        return $this->render('bc_vs/index.html.twig', [
            'b_c_vs' => $bCVsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="bcvs_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $bCV = new BCVs();
        $form = $this->createForm(BCVsType::class, $bCV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bCV);
            $entityManager->flush();

            return $this->redirectToRoute('b_c_vs_index');
        }

        return $this->render('bc_vs/new.html.twig', [
            'b_c_v' => $bCV,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bcvs_show", methods={"GET"})
     */
    public function show(BCVs $bCV): Response
    {
        return $this->render('bc_vs/show.html.twig', [
            'b_c_v' => $bCV,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bcvs_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BCVs $bCV): Response
    {
        $form = $this->createForm(BCVsType::class, $bCV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('b_c_vs_index', [
                'id' => $bCV->getId(),
            ]);
        }

        return $this->render('bc_vs/edit.html.twig', [
            'b_c_v' => $bCV,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bcvs_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BCVs $bCV): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bCV->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bCV);
            $entityManager->flush();
        }

        return $this->redirectToRoute('b_c_vs_index');
    }

    /**
     * @Route("/add", name="bcvs_add", methods="GET|POST")
     */
    public function bcvs_add(Request $request) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $patient_id = $request->request->get('patient');
            if ($em->getRepository(BCVs::class)->findSameDate($date[2], $date[1], $date[0], $patient_id) != [])
                return new JsonResponse(0);

            $bcvs = new BCVs();
            $bcvs->setDate($new_date);
            $bcvs->setAccompagnant($request->request->get('accompagnant'));
            $bcvs->setPermission($request->request->get('permission'));
            $bcvs->setEtat($request->request->get('etat'));
            $bcvs->setMotifRefus($request->request->get('motifRefus'));
            $bcvs->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($bcvs);
            $em->flush();
            return new JsonResponse($bcvs->getId());
        }
    }

    /**
     * @Route("/date_error", name="bcvs_date_error", methods="GET|POST")
     */
    public function bcvs_date_error(Request $request) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $bool = false;
            $id = $request->request->get('id');
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            $now = date_create(date('y-m-d'));

            $em = $this->getDoctrine()->getManager();
            $bcvss = $em->getRepository(Patient::class)->find($id)->getBcvs();

            if (date_diff($now, $new_date, false)->invert) {
                $bool = true;
            }
            foreach ($bcvss as $bcvs) {
                $date = date_create($bcvs->getDate()->format('y-m-d'));
                if (!date_diff($new_date, $date, false)->invert) {
                    $bool = true;
                }
            }
            return new JsonResponse($bool);
        }
    }

    /**
     * @Route("/remove/{id}", name="bcvs_remove", methods="DELETE")
     */
    public function bcvs_remove(Request $request, BCVs $bcvs) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($bcvs) {
                $em->remove($bcvs);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/patch/{id}", name="bcvs_patch", methods="GET|POST")
     */
    public function bcvs_patch(Request $request, BCVs $bcvs) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($bcvs) {
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

                $patient_id = $request->request->get('patient');
                $t = $em->getRepository(BCVs::class)->findSameDate($date[2], $date[1], $date[0], $patient_id);
                if ($t != [] && $t[0]->getDate() != $bcvs->getDate())
                    return new JsonResponse(0);

                $bcvs->setDate($new_date);
                $bcvs->setAccompagnant($request->request->get('accompagnant'));
                $bcvs->setPermission($request->request->get('permission'));
                $bcvs->setEtat($request->request->get('etat'));
                $bcvs->setMotifRefus($request->request->get('motifRefus'));
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }
}
