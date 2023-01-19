<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity()
    {
        $user = new User;
        $user->setUsername('lili');
        $user->setPassword('lilipassword');
        $user->setEmail('lili@test.com');
        $user->setRoles(['ROLE_USER']);


        $this->assertEquals('lili', $user->getUsername());
        $this->assertEquals('lilipassword', $user->getPassword());
        $this->assertEquals('lili@test.com', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

}