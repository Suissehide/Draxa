<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Patient;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/add", name="atelier_add", methods="GET|POST")
     */
    public function atelier_add(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $new_time = null;
            if ($request->request->get('time') != "") { 
                $time = explode(':', $request->request->get('time'));
                $new_time = date_create('2019-01-01')->setTime($time[0], $time[1]);
            }
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            
            if ($em->getRepository(Atelier::class)->findSameDate($date[2], $date[1], $date[0]) != [])
                return new JsonResponse(0);

            $atelier = new Atelier();
            $atelier->setDate($new_date);
            $atelier->setThematique($request->request->get('thematique'));
            $atelier->setHeure($new_time);
            $atelier->setType($request->request->get('type'));
            $atelier->setAccompagnant($request->request->get('accompagnant'));
            $atelier->setEtat($request->request->get('etat'));
            $atelier->setMotifRefus($request->request->get('motifRefus'));
            $atelier->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($atelier);
            $em->flush();
            return new JsonResponse($atelier->getId());
        }
    }

    /**
     * @Route("/patch/{id}", name="atelier_patch", methods="GET|POST")
     */
    public function atelier_patch(Request $request, Atelier $atelier): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($atelier) {
                $new_time = null;
                if ($request->request->get('time') != '') { 
                    $time = explode(':', $request->request->get('time'));
                    $new_time = date_create('2019-01-01')->setTime($time[0], $time[1]);
                }
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

                $t = $em->getRepository(Atelier::class)->findSameDate($date[2], $date[1], $date[0]);
                if ($t != [] && $t[0]->getDate() != $atelier->getDate())
                    return new JsonResponse(0);

                $atelier->setDate($new_date);
                $atelier->setThematique($request->request->get('thematique'));
                $atelier->setHeure($new_time);
                $atelier->setType($request->request->get('type'));
                $atelier->setAccompagnant($request->request->get('accompagnant'));
                $atelier->setEtat($request->request->get('etat'));
                $atelier->setMotifRefus($request->request->get('motifRefus'));
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/date_error", name="atelier_date_error", methods="GET|POST")
     */
    public function atelier_date_error(Request $request): JsonResponse
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
    public function atelier_remove(Request $request, Atelier $atelier): JsonResponse
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
     * OLD ROUTE
     */

    /**
     * @Route("/new", name="atelier_new", methods="GET|POST")
     */
    function new (Request $request): Response {
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
        if ($this->isCsrfTokenValid('delete' . $atelier->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($atelier);
            $em->flush();
        }

        return $this->redirectToRoute('atelier_index');
    }
}
