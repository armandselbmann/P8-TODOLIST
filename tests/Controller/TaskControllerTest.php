<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    /**
     * @return void
     */
    public function testDisplayTaskList(): void
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('div.thumbnail');
        $this->assertSelectorExists('.btn', 'Créer une tâche');
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
     */
    public function testTaskEditIsDone(): void
    {
        $crawler = $this->client->request('GET', '/tasks/1/edit');

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
     */
    public function testTaskToogleIsDone(): void
    {
        $taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $task = $taskRepository->find(1);
        $taskId = $task->getId();
        $taskStatusBefore = $task->isDone();
        $this->client->request('GET', "/tasks/$taskId/toggle");
        $taskStatusAfter = $task->isDone();

        $this->assertNotSame($taskStatusBefore, $taskStatusAfter);
        $this->assertIsBool($taskStatusAfter);
        $this->assertIsBool($taskStatusBefore);
    }

}