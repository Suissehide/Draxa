<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Patient;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/atelier")
 */
class AtelierController extends AbstractController
{
    /**
     * @Route("/", name="atelier_index", methods="GET")
     */
    public function index(AtelierRepository $atelierRepository): Response
    {
        return $this->render('atelier/index.html.twig', ['ateliers' => $atelierRepository->findAll()]);
    }

    /**
     * @Route("/new", name="atelier_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($atelier);
            $em->flush();

            return $this->redirectToRoute('atelier_index');
        }

        return $this->render('atelier/new.html.twig', [
            'atelier' => $atelier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="atelier_show", methods="GET")
     */
    public function show(Atelier $atelier): Response
    {
        return $this->render('atelier/show.html.twig', ['atelier' => $atelier]);
    }

    /**
     * @Route("/{id}/edit", name="atelier_edit", methods="GET|POST")
     */
    public function edit(Request $request, Atelier $atelier): Response
    {
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('atelier_edit', ['id' => $atelier->getId()]);
        }

        return $this->render('atelier/edit.html.twig', [
            'atelier' => $atelier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="atelier_delete", methods="DELETE")
     */
    public function delete(Request $request, Atelier $atelier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$atelier->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($atelier);
            $em->flush();
        }

        return $this->redirectToRoute('atelier_index');
    }

    /**
     * @Route("/add", name="atelier_add", methods="GET|POST")
     */
    public function atelier_add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $atelier = new Atelier();
            $atelier->setDate($new_date);
            $atelier->setType($request->request->get('type'));
            $atelier->setAccompagnant($request->request->get('accompagnant'));
            $atelier->setMotifRefus($request->request->get('motifRefus'));
            $atelier->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($atelier);
            $em->flush();
            return new JsonResponse($atelier->getId());
        }
    }

    /**
     * @Route("/date_error", name="atelier_date_error", methods="GET|POST")
     */
    public function atelier_date_error(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $bool = false;
            $id = $request->request->get('id');
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            $now = date_create(date('y-m-d'));

            $em = $this->getDoctrine()->getManager();
            $ateliers = $em->getRepository(Patient::class)->find($id)->getAteliers();

            if (date_diff($now, $new_date, false)->invert) {
                $bool = true;
            }
            foreach ($ateliers as $atelier) {
                $date = date_create($atelier->getDate()->format('y-m-d'));
                if (!date_diff($new_date, $date, false)->invert) {
                    $bool = true;
                }
            }
            return new JsonResponse($bool);
        }
    }

    /**
     * @Route("/remove/{id}", name="atelier_remove", methods="DELETE")
     */
    public function atelier_remove(Request $request, Atelier $atelier) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($atelier) {
                $em->remove($atelier);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

        /**
     * @Route("/patch/{id}", name="atelier_patch", methods="GET|POST")
     */
    public function atelier_patch(Request $request, Atelier $atelier) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($atelier) {
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
                $atelier->setDate($new_date);
                $atelier->setType($request->request->get('type'));
                $atelier->setAccompagnant($request->request->get('accompagnant'));
                $atelier->setMotifRefus($request->request->get('motifRefus'));
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }
}
