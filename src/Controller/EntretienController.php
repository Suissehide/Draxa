<?php

namespace App\Controller;

use App\Entity\Entretien;
use App\Entity\Patient;
use App\Form\EntretienType;
use App\Repository\EntretienRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/entretien")
 */
class EntretienController extends AbstractController
{
    /**
     * @Route("/", name="entretien_index", methods="GET")
     */
    public function index(EntretienRepository $entretienRepository) : Response
    {
        return $this->render('entretien/index.html.twig', ['entretiens' => $entretienRepository->findAll()]);
    }

    /**
     * @Route("/new", name="entretien_new", methods="GET|POST")
     */
    public function new(Request $request) : Response
    {
        $entretien = new Entretien();
        $form = $this->createForm(EntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entretien);
            $em->flush();

            return $this->redirectToRoute('entretien_index');
        }

        return $this->render('entretien/new.html.twig', [
            'entretien' => $entretien,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entretien_show", methods="GET")
     */
    public function show(Entretien $entretien) : Response
    {
        return $this->render('entretien/show.html.twig', ['entretien' => $entretien]);
    }

    /**
     * @Route("/{id}/edit", name="entretien_edit", methods="GET|POST")
     */
    public function edit(Request $request, Entretien $entretien) : Response
    {
        $form = $this->createForm(EntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entretien_edit', ['id' => $entretien->getId()]);
        }

        return $this->render('entretien/edit.html.twig', [
            'entretien' => $entretien,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entretien_delete", methods="DELETE")
     */
    public function delete(Request $request, Entretien $entretien) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $entretien->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entretien);
            $em->flush();
        }

        return $this->redirectToRoute('entretien_index');
    }

    /**
     * @Route("/add", name="entretien_add", methods="GET|POST")
     */
    public function entretien_add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $new_time = null;
            if ($request->request->get('time') != '') { 
                $time = explode(':', $request->request->get('time'));
                $new_time->setTime($time[0], $time[1]);
            }
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $entretien = new Entretien();
            $entretien->setDate($new_date);
            $entretien->setThematique($request->request->get('thematique'));
            $entretien->setHeure($new_time);
            $entretien->setType($request->request->get('type'));
            $entretien->setAccompagnant($request->request->get('accompagnant'));
            $entretien->setEtat($request->request->get('etat'));
            $entretien->setMotifRefus($request->request->get('motifRefus'));
            $entretien->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($entretien);
            $em->flush();
            return new JsonResponse($entretien->getId());
        }
    }

    /**
     * @Route("/date_error", name="entretien_date_error", methods="GET|POST")
     */
    public function entretien_date_error(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $bool = false;
            $id = $request->request->get('id');
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            $now = date_create(date('y-m-d'));

            $em = $this->getDoctrine()->getManager();
            $entretiens = $em->getRepository(Patient::class)->find($id)->getEntretiens();

            if (date_diff($now, $new_date, false)->invert) {
                $bool = true;
            }
            foreach ($entretiens as $entretien) {
                $date = date_create($entretien->getDate()->format('y-m-d'));
                if (!date_diff($new_date, $date, false)->invert) {
                    $bool = true;
                }
            }
            return new JsonResponse($bool);
        }
    }

    /**
     * @Route("/remove/{id}", name="entretien_remove", methods="DELETE")
     */
    public function entretien_remove(Request $request, Entretien $entretien) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($entretien) {
                $em->remove($entretien);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/patch/{id}", name="entretien_patch", methods="GET|POST")
     */
    public function entretien_patch(Request $request, Entretien $entretien) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($entretien) {
                $new_time = null;
                if ($request->request->get('time') != '') { 
                    $time = explode(':', $request->request->get('time'));
                    $new_time->setTime($time[0], $time[1]);
                }
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
    
                $entretien->setDate($new_date);
                $entretien->setThematique($request->request->get('thematique'));
                $entretien->setHeure($new_time);
                $entretien->setType($request->request->get('type'));
                $entretien->setAccompagnant($request->request->get('accompagnant'));
                $entretien->setEtat($request->request->get('etat'));
                $entretien->setMotifRefus($request->request->get('motifRefus'));
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

}
