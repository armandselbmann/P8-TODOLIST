<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // create 2 users
        $user1 = new User();
        $user1->setUsername('lolo');
        $user1->setPassword($this->userPasswordHasher->hashPassword($user1, 'lolo'));
        $user1->setEmail('lolo@gmail.com');
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('jane');
        $user2->setPassword($this->userPasswordHasher->hashPassword($user2, 'jane'));
        $user2->setEmail('jane@gmail.com');
        $user2->setRoles(['ROLE_ADMIN']);
        $manager->persist($user2);

        // create 40 tasks
        for ($i = 0; $i < 40; $i++) {
            $task = new Task();
            $task->setTitle('task ' . $i);
            $task->setContent('content ' . $i);
            $task->toggle(rand(0, 1));
            $randInt = rand(0, 2);
            if ($randInt == 1) {
                $user = $user1;
            } elseif ($randInt == 2) {
                $user = $user2;
            } else {
                $user = null;
            }
            $task->setUser($user);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
