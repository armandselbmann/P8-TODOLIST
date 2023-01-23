<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('lolo');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->followRedirects();
    }

    /**
     * @return void
     */
    public function testDisplayTasksList(): void
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2',"Liste de l'ensemble de vos tâches" );
    }

    /**
     * @return void
     */
    public function testDisplayTasksTodoList(): void
    {
        $this->client->request('GET', '/tasks/todo');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2',"Liste de l'ensemble de vos tâches à faire" );
    }

    /**
     * @return void
     */
    public function testDisplayTasksDoneList(): void
    {
        $this->client->request('GET', '/tasks/done');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2',"Liste de l'ensemble de vos tâches terminées" );
    }

    /**
     * @return void
     */
    public function testBackToTaskListFromTaskCreate(): void
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $link = $crawler->selectLink('Retour à la liste des tâches')->link();
        $this->client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return void
     */
    public function testTaskCreationWorked(): void
    {
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Tâche pour test';
        $form['task[content]'] = 'Un contenu de test';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            'La tâche a été bien été ajoutée'
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskEditIsDone(): void
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '1', 'isDone' => '1'), array(), 1);
        $task = $taskRepository->find($task[0]->getId());
        $taskId = $task->getId();


        $crawler = $this->client->request('GET', "/tasks/$taskId/edit");

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Tâche pour un test de modification';
        $form['task[content]'] = 'Un contenu de test de modification';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            'Superbe ! La tâche a bien été modifiée.'
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskEditIsProhibitedForThisUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('lolo');
        $this->client->loginUser($testUser);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '2', 'isDone' => '1'), array(), 1);
        $task = $taskRepository->find($task[0]->getId());
        $taskId = $task->getId();
        $this->client->request('GET', "/tasks/$taskId/edit");

        $this->assertSelectorTextContains(
            'div.alert.alert-danger',
            'Oops ! Vous ne pouvez pas modifier cette tâche.'
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskToggleIsDone(): void
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '1', 'isDone' => '0'), array(), 1);
        $task = $taskRepository->find($task[0]->getId());
        $taskId = $task->getId();
        $taskTitle = $task->getTitle();
        $taskStatusBefore = $task->isDone();
        $this->client->request('GET', "/tasks/$taskId/toggle");
        $taskStatusAfter = $task->isDone();

        $this->assertNotSame($taskStatusBefore, $taskStatusAfter);
        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            "Superbe ! La tâche $taskTitle a bien été marquée comme faite."
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskToggleIsToDo(): void
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '1', 'isDone' => '1'), array(), 1);
        $task = $taskRepository->find($task[0]->getId());
        $taskId = $task->getId();
        $taskTitle = $task->getTitle();
        $taskStatusBefore = $task->isDone();
        $this->client->request('GET', "/tasks/$taskId/toggle");
        $taskStatusAfter = $task->isDone();

        $this->assertNotSame($taskStatusBefore, $taskStatusAfter);
        $this->assertSelectorTextContains(
            'div.alert.alert-danger',
            "Oops ! La tâche $taskTitle a bien été marquée comme non terminée."
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskToggleIsProhibitedForThisUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('lolo');
        $this->client->loginUser($testUser);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '2', 'isDone' => '1'), array(), 1);
        $task = $taskRepository->find($task[0]->getId());
        $taskId = $task->getId();
        $taskStatusBefore = $task->isDone();
        $this->client->request('GET', "/tasks/$taskId/toggle");

        $this->assertSelectorTextContains(
            'div.alert.alert-danger',
            'Oops ! Vous n\'avez pas accès à cette tâche.'
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskDelete(): void
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '1'), array(), 1);
        $taskId = $task[0]->getId();
        $this->client->request('GET', "/tasks/$taskId/delete");

        $this->assertSelectorTextContains(
            'div.alert.alert-success',
            'La tâche a bien été supprimée.'
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTaskDeleteIsProhibitedForThisUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('lolo');
        $this->client->loginUser($testUser);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findBy(array('user' => '2'), array(), 1);
        $taskId = $task[0]->getId();
        $this->client->request('GET', "/tasks/$taskId/delete");

        $this->assertSelectorTextContains(
            'div.alert.alert-danger',
            'Oops ! Vous ne pouvez pas supprimer cette tâche.'
        );
    }

}