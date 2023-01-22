<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('jane');
        // simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $this->client->followRedirects();
    }

    public function testDisplayUserList(): void
    {
        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');
    }


    public function testUserCreationWorked(): void
    {
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'John';
        $form['user[roles]'] = "ROLE_USER";
        $form['user[password][first]'] = '1234';
        $form['user[password][second]'] = '1234';
        $form['user[email]'] = 'john@doe.com';

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur a bien été ajouté.');
    }

    public function testUserEditIsDone(): void
    {
        $crawler = $this->client->request('GET', '/users/1/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'lolo';
        $form['user[roles]'] = "ROLE_USER";
        $form['user[password][first]'] = 'lolo';
        $form['user[password][second]'] = 'lolo';
        $form['user[email]'] = 'lolo@izeron.fr';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur a bien été modifié');
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testDeleteUserByAdmin(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('John');
        $userId = $user->getId();
        $this->client->request('GET', "/users/$userId/delete");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('div.alert.alert-success', "Superbe ! L’utilisateur " . $user->getUsername() . " a bien été supprimé.");
    }

}