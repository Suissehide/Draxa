<?php

namespace App\Controller;

use App\Entity\Todo;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/todo")
 */
class TodoController extends AbstractController
{
    /**
     * @Route("/getTodos", name="todo_get", methods="GET")
     */
    public function getTodos(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $todoObjects = $em->getRepository(Todo::class)->findAll();

        if ($request->isXmlHttpRequest()) {
            $todos = [];
            foreach($todoObjects as $t) {
                $todos[] = [
                    'id' => $t->getId(),
                    'enable' => $t->getEnable(),
                    'description' => $t->getDescription(),
                    'datetime' => $t->getDatetime()->format('d/m/Y H:i'),
                ];
            }

            return new JsonResponse($todos);
        }
    }

    
    /**
     * @Route("/add", name="todo_add", methods="GET|POST")
     */
    public function add(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $todo = new Todo();

            $description = $request->request->get('description');

            $todo->setEnable(false);
            $todo->setDatetime(new \DateTime());
            $todo->setDescription($description);

            $em->persist($todo);
            $em->flush();

            return new JsonResponse($todo->getId());
        }
    }

    /**
     * @Route("/remove", name="todo_remove", methods="GET|POST")
     */
    public function remove(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $taskId = $request->request->get('taskId');

            $todo = $em->getRepository(Todo::class)->find($taskId);

            $em->remove($todo);
            $em->flush();

            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/clear", name="todo_clear", methods="GET|POST")
     */
    public function clear(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $todos = $em->getRepository(Todo::class)->findAll();
            foreach($todos as $todo) {
                $em->remove($todo);
            }
            $em->flush();

            return new JsonResponse(true);
        }
    }

    /**
     * @Route("/enable", name="todo_enable", methods="GET|POST")
     */
    public function enable(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $enable = $request->request->get('enable');
            $taskId = $request->request->get('taskId');

            $todo = $em->getRepository(Todo::class)->find($taskId);
            $todo->setEnable($enable);
            $em->flush();

            return new JsonResponse(true);
        }
    }
}
