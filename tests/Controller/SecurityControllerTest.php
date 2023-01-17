<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private ?object $urlGenerator;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    public function testDisplayLogin(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('input[id="username"]');
        $this->assertSelectorExists('input[id="password"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'lolo',
            '_password' => 'fake'
        ]);
        $this->client->submit($form);

        $this->assertSelectorExists('div.alert.alert-danger');
    }

    public function testLoginWithGoodCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'lolo',
            '_password' => 'lolo'
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.btn-danger', 'Se déconnecter');
    }

}
