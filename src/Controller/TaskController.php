<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    #[Route('/tasks', name: 'task_list')]
    public function listTasks(TaskRepository $taskRepository): Response
    {
        return $this->render(
            'task/list.html.twig',
            ['tasks' => $taskRepository->findByUser(
                array('user' => $this->getUser()->getId()),
                array('createdAt' => 'DESC')
            )]
        );
    }

    /**
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    #[Route('/tasks/done', name: 'task_list_done')]
    public function listTasksDone(TaskRepository $taskRepository): Response
    {
        return $this->render(
            'task/list.html.twig',
            ['tasks' => $taskRepository->findBy(
                array(
                    'user' => $this->getUser()->getId(),
                    'isDone' => '1'),
                array('createdAt' => 'DESC')
            )
            ]
        );
    }

    /**
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    #[Route('/tasks/todo', name: 'task_list_todo')]
    public function listTaskTodo(TaskRepository $taskRepository): Response
    {
        return $this->render(
            'task/list.html.twig',
            ['tasks' => $taskRepository->findBy(
                array(
                    'user' => $this->getUser()->getId(),
                    'isDone' => '0'),
                array('createdAt' => 'DESC')
            )
            ]
        );
    }

    /**
     * @param TaskRepository $taskRepository
     *
     * @return Response
     */
    #[Route('/tasks/manage', name: 'task_manage')]
    #[IsGranted('ROLE_ADMIN', message: "Espace réservé aux administrateurs.")]
    public function listTaskManage(TaskRepository $taskRepository): Response
    {
        return $this->render(
            'task/manage.html.twig',
            ['tasks' => $taskRepository->findAll()]
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request): RedirectResponse|Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form]
        );
    }

    /**
     * @param Task     $task
     * @param Request  $request
     * @param Security $security
     *
     * @return RedirectResponse|Response
     */
    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(
        Task $task,
        Request $request,
        Security $security
    ): RedirectResponse|Response {

        if ($security->isGranted('ROLE_USER') && !$security->isGranted('ROLE_ADMIN')) {
            if ($task->getUser() !== $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas modifier cette tâche.');
                return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
            }
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->flush();
                $this->addFlash('success', 'La tâche a bien été modifiée.');
                return $this->redirectToRoute('task_list');
            }
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            if ($task->getUser() !== $this->getUser()) {
                return $this->redirectToRoute('task_manage');
            }
            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/edit.html.twig',
            [
            'form' => $form,
            'task' => $task,]
        );
    }

    /**
     * @param Task $task
     *
     * @return RedirectResponse
     */
    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        if ($task->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette tâche.');
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        if($task->isIsDone() == true) {
            $this->addFlash(
                'success',
                sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle())
            );
        } else {
            $this->addFlash(
                'error',
                sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle())
            );
        }

        return $this->redirectToRoute('task_list');
    }

    /**
     * @param Task     $task
     * @param Security $security
     *
     * @return RedirectResponse
     */
    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task, Security $security): RedirectResponse
    {

        if ($security->isGranted('ROLE_USER') && !$security->isGranted('ROLE_ADMIN')) {
            if ($task->getUser() !== $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer cette tâche.');
                return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
            }
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('task_list');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        if ($task->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('task_manage');
        }
        return $this->redirectToRoute('task_list');

    }
}
