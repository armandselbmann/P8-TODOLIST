<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    #[Route('/users', name: 'user_list')]
    #[IsGranted('ROLE_ADMIN', message: "Espace réservé aux administrateurs.")]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render(
            'user/list.html.twig',
            ['users' => $userRepository->findAll()]
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    #[Route('/users/create', name: 'user_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/create.html.twig',
            ['form' => $form]
        );
    }

    /**
     *
     * @param User    $user
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    #[Route('/users/{id}/edit', name: 'user_edit')]
    #[IsGranted('ROLE_ADMIN', message: "Espace réservé aux administrateurs.")]
    public function editAction(
        User $user,
        Request $request
    ): RedirectResponse|Response {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($password);

            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render(
            'user/edit.html.twig',
            ['form' => $form, 'user' => $user]
        );
    }

    /**
     * @param User $user
     *
     * @return RedirectResponse
     */
    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: "Espace réservé aux administrateurs.")]
    public function deleteAction(
        User $user,
    ): RedirectResponse {

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', "L’utilisateur " . $user->getUserIdentifier() . " a bien été supprimé.");
        return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
    }
}
