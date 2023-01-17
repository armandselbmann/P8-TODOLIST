<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // create 40 tasks
        for ($i = 0; $i < 40; $i++) {
            $task = new Task();
            $task->setTitle('task '.$i);
            $task->setContent('content '.$i);
            $task->toggle(rand(0, 1));
            $manager->persist($task);
        }

        // create 2 users
        $user = new User();
        $user->setUsername('lolo');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'lolo'));
        $user->setEmail('lolo@gmail.com');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('jane');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'jane'));
        $user->setEmail('jane@gmail.com');
        $manager->persist($user);

        $manager->flush();
    }
}
