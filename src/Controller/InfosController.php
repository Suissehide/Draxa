<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Infos;
use App\Form\InfosType;
use App\Repository\InfosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/infos")
 */
class InfosController extends AbstractController
{
    /**
     * @Route("/", name="infos_index", methods={"GET"})
     */
    public function index(InfosRepository $infosRepository): Response
    {
        return $this->render('infos/index.html.twig', [
            'infos' => $infosRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="infos_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $info = new Infos();
        $form = $this->createForm(InfosType::class, $info);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($info);
            $entityManager->flush();

            return $this->redirectToRoute('infos_index');
        }

        return $this->render('infos/new.html.twig', [
            'info' => $info,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="infos_show", methods={"GET"})
     */
    public function show(Infos $info): Response
    {
        return $this->render('infos/show.html.twig', [
            'info' => $info,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="infos_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Infos $info): Response
    {
        $form = $this->createForm(InfosType::class, $info);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('infos_index', [
                'id' => $info->getId(),
            ]);
        }

        return $this->render('infos/edit.html.twig', [
            'info' => $info,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="infos_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Infos $info): Response
    {
        if ($this->isCsrfTokenValid('delete'.$info->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($info);
            $entityManager->flush();
        }

        return $this->redirectToRoute('infos_index');
    }

    /**
     * @Route("/add", name="infos_add", methods="GET|POST")
     */
    public function infos_add(Request $request) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $infos = new Infos();
            $infos->setDate($new_date);
            $infos->setType($request->request->get('type'));
            $infos->setAccompagnant($request->request->get('accompagnant'));
            $infos->setEtat($request->request->get('etat'));
            $infos->setMotifRefus($request->request->get('motifRefus'));
            $infos->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($infos);
            $em->flush();
            return new JsonResponse($infos->getId());
        }
    }

    /**
     * @Route("/date_error", name="infos_date_error", methods="GET|POST")
     */
    public function infos_date_error(Request $request) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $bool = false;
            $id = $request->request->get('id');
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            $now = date_create(date('y-m-d'));

            $em = $this->getDoctrine()->getManager();
            $infoss = $em->getRepository(Patient::class)->find($id)->getInfos();

            if (date_diff($now, $new_date, false)->invert) {
                $bool = true;
            }
            foreach ($infoss as $infos) {
                $date = date_create($infos->getDate()->format('y-m-d'));
                if (!date_diff($new_date, $date, false)->invert) {
                    $bool = true;
                }
            }
            return new JsonResponse($bool);
        }
    }

    /**
     * @Route("/remove/{id}", name="infos_remove", methods="DELETE")
     */
    public function infos_remove(Request $request, Infos $infos) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($infos) {
                $em->remove($infos);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/patch/{id}", name="infos_patch", methods="GET|POST")
     */
    public function infos_patch(Request $request, Infos $infos) : JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($infos) {
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
                $infos->setDate($new_date);
                $infos->setType($request->request->get('type'));
                $infos->setAccompagnant($request->request->get('accompagnant'));
                $infos->setEtat($request->request->get('etat'));
                $infos->setMotifRefus($request->request->get('motifRefus'));
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }
}