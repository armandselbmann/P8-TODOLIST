<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    /**
     * @return void
     * @throws Exception
     */
    public function testAddUser(): void
    {
        $user = new User();
        $user->setUsername('tutu');
        $user->setPassword('tutupassword');
        $user->setEmail('tutu@tutu.com');
        $user->setRoles(['ROLE_USER']);

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->add($user, true);

        $this->assertEquals('tutu', $user->getUserIdentifier());
        $this->assertEquals('tutupassword', $user->getPassword());
        $this->assertEquals('tutu@tutu.com', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRemoveUser(): void
    {
        self::bootKernel();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['username' => 'lolo']);
        $this->assertInstanceOf(User::class, $user);

        $userRepository->remove($user, true);

        $this->assertNull($userRepository->findOneBy(['username' => 'lolo']));
    }

}