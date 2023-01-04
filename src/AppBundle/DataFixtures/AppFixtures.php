<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 40 tasks
        for ($i = 0; $i < 40; $i++) {
            $task = new Task();
            $task->setTitle('task '.$i);
            $task->setContent('content '.$i);
            $task->toggle(rand(0, 1));
            $manager->persist($task);
        }
        $manager->flush();
    }
}
