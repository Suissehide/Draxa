<?php

namespace App\Controller;

use App\Entity\AppUser;

use App\Form\AppUserFormType;
use App\Form\AppUserType;

use App\Repository\AppUserRepository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/")
 */
class AppUserController extends AbstractController
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
     * @Route("/default", name="default", methods="GET")
     */
    function default(): Response {
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/login", name="login", methods="GET|POST")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('app_user/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods="GET")
     */
    public function logout(): Response
    {
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/register", name="register", methods="GET|POST")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, AppUserRepository $appUserRepository): Response
    {
        $session = $request->getSession();
        $user = new AppUser();
        $form = $this->createForm(AppUserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('validation')->isClicked()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                // $user->setRoles(array('ROLE_USER'));
                $this->em->persist($user);
                $this->em->flush();
                $session->getFlashBag()->add('success', 'Félicitations ! Votre compte a été créé avec succès !');

                return $this->redirectToRoute('login');
            }
        }
        return $this->render('app_user/register.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/app/user/index", name="app_user_index", methods="GET")
     */
    public function index(AppUserRepository $appUserRepository): Response
    {
        return $this->render('app_user/index.html.twig', ['app_users' => $appUserRepository->findAll()]);
    }

    /**
     * @Route("/app/user/new", name="app_user_new", methods="GET|POST")
     */
    function new (Request $request): Response {
        $appUser = new AppUser();
        $form = $this->createForm(AppUserType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($appUser);
            $this->em->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('app_user/new.html.twig', [
            'app_user' => $appUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/app/user/{id}", name="app_user_show", methods="GET")
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $appUser = $doctrine->getRepository(AppUser::class)->find($id);
        return $this->render('app_user/show.html.twig', ['app_user' => $appUser]);
    }

    /**
     * @Route("/app/user/{id}/edit", name="app_user_edit", methods="GET|POST")
     */
    public function edit(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $appUser = $doctrine->getRepository(AppUser::class)->find($id);
        $form = $this->createForm(AppUserType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_user_edit', ['id' => $appUser->getId()]);
        }

        return $this->render('app_user/edit.html.twig', [
            'app_user' => $appUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/app/user/{id}", name="app_user_delete", methods="DELETE")
     */
    public function delete(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $appUser = $doctrine->getRepository(AppUser::class)->find($id);
        if ($this->isCsrfTokenValid('delete' . $appUser->getId(), $request->get('_token'))) {
            $this->em->remove($appUser);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }
}
