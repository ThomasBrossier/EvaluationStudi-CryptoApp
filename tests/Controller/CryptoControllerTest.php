<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CryptoControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.noCrypto');
        $this->assertSelectorExists('.transaction_row');
        $this->assertSelectorExists('.add-btn');
        $this->assertSelectorTextContains('.amount > span',"€");
    }
    public function testAddPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ajouter-une-crypto');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('.form-row');
        $this->assertSelectorExists('.close-btn');
        $this->assertSelectorExists('.btn-main');
        $this->assertSelectorExists('#crypto');
        $this->assertSelectorExists('#quantity');
        $this->assertSelectorExists('#price');
        $this->assertSelectorTextSame('h1','Ajouter une transaction');
    }

    public function testAddFunctionalityKnownCrypto(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ajouter-une-crypto');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['quantity'] = 1.0;
        $form['crypto'] = 'BTC';
        $form['value'] = 190000;
        $client->submit($form);
        $response = $client->getResponse();

        $this->assertResponseIsSuccessful($response);
    }
    public function testAddFunctionalityUnknownCrypto(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ajouter-une-crypto');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['quantity'] = 1.0;
        $form['crypto'] = 'APT';
        $form['value'] = 190000;
        $client->submit($form);
        $response = $client->getResponse();

        $this->assertResponseIsSuccessful($response);
    }

}
