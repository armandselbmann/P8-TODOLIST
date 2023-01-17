<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{

    public function testTaskEntity()
    {
        $task = new Task;
        $task->setTitle('Title test');
        $task->setContent('Content test');
        $dateNow = new DateTime();
        $task->setCreatedAt($dateNow);
        $task->setIsDone(true);

        $this->assertEquals('Title test', $task->getTitle());
        $this->assertEquals('Content test', $task->getContent());
        $this->assertEquals($dateNow, $task->getCreatedAt());
        $this->assertEquals(true, $task->isIsDone());
    }

}
