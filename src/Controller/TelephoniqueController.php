<?php

namespace App\Controller;

use App\Entity\Telephonique;
use App\Entity\Patient;
use App\Form\TelephoniqueType;
use App\Repository\TelephoniqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/telephonique")
 */
class TelephoniqueController extends Controller
{
    /**
     * @Route("/", name="telephonique_index", methods="GET")
     */
    public function index(TelephoniqueRepository $telephoniqueRepository) : Response
    {
        return $this->render('telephonique/index.html.twig', ['telephoniques' => $telephoniqueRepository->findAll()]);
    }

    /**
     * @Route("/new", name="telephonique_new", methods="GET|POST")
     */
    public function new(Request $request) : Response
    {
        $telephonique = new Telephonique();
        $form = $this->createForm(TelephoniqueType::class, $telephonique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($telephonique);
            $em->flush();

            return $this->redirectToRoute('telephonique_index');
        }

        return $this->render('telephonique/new.html.twig', [
            'telephonique' => $telephonique,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="telephonique_show", methods="GET")
     */
    public function show(Telephonique $telephonique) : Response
    {
        return $this->render('telephonique/show.html.twig', ['telephonique' => $telephonique]);
    }

    /**
     * @Route("/{id}/edit", name="telephonique_edit", methods="GET|POST")
     */
    public function edit(Request $request, Telephonique $telephonique) : Response
    {
        $form = $this->createForm(TelephoniqueType::class, $telephonique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('telephonique_edit', ['id' => $telephonique->getId()]);
        }

        return $this->render('telephonique/edit.html.twig', [
            'telephonique' => $telephonique,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="telephonique_delete", methods="DELETE")
     */
    public function delete(Request $request, Telephonique $telephonique) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $telephonique->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($telephonique);
            $em->flush();
        }

        return $this->redirectToRoute('telephonique_index');
    }

    /**
     * @Route("/add", name="telephonique_add", methods="GET|POST")
     */
    public function telephonique_add(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');

            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));

            $telephonique = new Telephonique();
            $telephonique->setDate($new_date);
            $telephonique->setType($request->request->get('type'));
            $telephonique->setMotifRefus($request->request->get('motifRefus'));
            $telephonique->setPatient($em->getRepository(Patient::class)->findOneById($id));

            $em->persist($telephonique);
            $em->flush();
            return new JsonResponse($telephonique->getId());
        }
    }

    /**
     * @Route("/date_error", name="telephonique_date_error", methods="GET|POST")
     */
    public function telephonique_date_error(Request $request) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $bool = false;
            $id = $request->request->get('id');
            $date = explode('/', $request->request->get('date'));
            $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
            $now = date_create(date('y-m-d'));

            $em = $this->getDoctrine()->getManager();
            $telephoniques = $em->getRepository(Patient::class)->find($id)->getTelephoniques();

            if (date_diff($now, $new_date)->invert) {
                $bool = true;
            }
            foreach ($telephoniques as $telephonique) {
                $date = date_create($telephonique->getDate()->format('y-m-d'));
                if (!date_diff($new_date, $date)->invert) {
                    $bool = true;
                }
            }
            return new JsonResponse($bool);
        }
    }

    /**
     * @Route("/remove/{id}", name="telephonique_remove", methods="DELETE")
     */
    public function telephonique_remove(Request $request, Telephonique $telephonique) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($telephonique) {
                $em->remove($telephonique);
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/patch/{id}", name="telephonique_patch", methods="GET|POST")
     */
    public function telephonique_patch(Request $request, Telephonique $telephonique) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            if ($telephonique) {
                $date = explode('/', $request->request->get('date'));
                $new_date = date_create(date("y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2])));
                $telephonique->setDate($new_date);
                $telephonique->setType($request->request->get('type'));
                $telephonique->setMotifRefus($request->request->get('motifRefus'));
                $em->flush();
                return new JsonResponse(true);
            }
            return new JsonResponse(false);
        }
    }
}
