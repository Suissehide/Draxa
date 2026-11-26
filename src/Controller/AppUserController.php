<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Form\AppUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppUserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/login', name: 'login', methods: 'GET|POST')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('app_user/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Intercepté par le pare-feu (voir security.yaml).
     */
    #[Route(path: '/logout', name: 'logout', methods: 'GET')]
    public function logout(): Response
    {
        return $this->redirectToRoute('login');
    }

    #[Route(path: '/register', name: 'register', methods: 'GET|POST')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new AppUser();
        $form = $this->createForm(AppUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('validation')->isClicked()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Félicitations ! Votre compte a été créé avec succès !');

            return $this->redirectToRoute('login');
        }

        return $this->render('app_user/register.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView(),
        ]);
    }
}
