<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Cette classe permet de tester l'authenticator de l'application.
 *
 * @todo  completer ce test pour améliorer la couverture du code.
 **/
class AppAuthenticatorTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->repo = static::getContainer()->get(UserRepository::class);
    }

    public function testIsHomeAccessible(): void
    {
        $testUser = $this->repo->findOneBy(['email'=>'test@test.fr']);
        $this->client->loginUser($testUser);
        $this->client->request('GET','/');

        $this->assertResponseIsSuccessful();
    }
    public function testIsHomeForbidden(): void
    {
        $this->client->request('GET','/');
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
        $this->assertRouteSame('app_login');
    }
}
