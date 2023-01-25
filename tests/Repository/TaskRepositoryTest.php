<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testRemoveTask(): void
    {
        self::bootKernel();

        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $task = $taskRepository->findOneBy(['id' => '10']);
        $this->assertInstanceOf(Task::class, $task);

        $taskRepository->remove($task, true);

        $this->assertNull($taskRepository->findOneBy(['id' => '10']));
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSaveTask(): void
    {
        $task = new Task;
        $task->setTitle('Title test');
        $task->setContent('Content test');
        $dateNow = new DateTime();
        $task->setCreatedAt($dateNow);
        $task->setIsDone(true);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskRepository->save($task, true);

        $this->assertEquals('Title test', $task->getTitle());
        $this->assertEquals('Content test', $task->getContent());
        $this->assertEquals($dateNow, $task->getCreatedAt());
        $this->assertEquals(true, $task->isIsDone());
    }
}