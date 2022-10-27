<?php

namespace App\Tests\Controller;

use App\Entity\CryptoMoney;
use App\Entity\Transaction;
use App\Repository\CryptoMoneyRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;

class CryptoControllerTest extends WebTestCase
{

    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.noCrypto');
        $this->assertSelectorExists('.transaction_row');
        $this->assertSelectorExists('.add-btn');
        $this->assertSelectorTextContains('.amount > span',"€");
    }



    /*====================== ADD ROUTE========================*/
    /*Teste la vue de la page d'ajout*/
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


    /*Teste l'ajout d'une crypto qui est déjà dans la base de données*/
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

    /*Teste l'ajout d'une crypto qui n'est pas dans  la base de données*/
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


    /*Teste si une erreur apparait en cas de quantité erronée*/
    public function testAddFunctionalityQuantityError(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/ajouter-une-crypto');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['quantity'] = "abc";
        $form['crypto'] = 'BTC';
        $form['value'] = 1000;
        $client->submit($form);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful($response);
        $this->assertSelectorExists('.alert-danger');
    }





    /*====================== DELETE ROUTE========================*/

    /*Teste la vue de la page de suppression */
    public function testDeletePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/supprimer-une-crypto');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('.form-row');
        $this->assertSelectorExists('.close-btn');
        $this->assertSelectorExists('.btn-main');
        $this->assertSelectorExists('#crypto');
        $this->assertSelectorExists('#quantity');
        $this->assertSelectorExists('#price');
        $this->assertSelectorTextSame('h1','Supprimer un montant');
    }

    /*Teste si la suppression se fait sur la base de données*/
    public function testDeleteFunctionality(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/supprimer-une-crypto');
        $cryptoMoneyRepository = static::getContainer()->get(CryptoMoneyRepository::class);
        $transactionRepository = static::getContainer()->get(TransactionRepository::class);
        $crypto = $cryptoMoneyRepository->findBy(['symbol' => "BTC"]);
        if($crypto){
            $crypto = $crypto[0];
            foreach ($crypto->getTransactions() as $transaction ){
                $transactionRepository->remove($transaction, true);
            }
        }else{
            $crypto = new CryptoMoney();
            $crypto->setSymbol('BTC')
                ->setLogoLink('XXXX')
                ->setTitle('Bitcoin');
        }

        $transaction = new Transaction();
        $transaction->setCreatedAt(new \DateTimeImmutable())
            ->setUnitPrice(10000)
            ->setQuantity(4)
            ->setType('purchase');
        $crypto->addTransaction($transaction);
        $cryptoMoneyRepository->save($crypto,true);
        $form = $crawler->selectButton('Supprimer')->form();
        $form['quantity'] = 2.0;
        $form['crypto'] = 'BTC';
        $form['value'] = 190000;
        $form['unitPrice'] = 190000;
        $client->submit($form);
        $getcrypto = $cryptoMoneyRepository->findBySymbol(['symbol'=>'BTC'])[0];
        $this->assertEquals(2,$getcrypto->getTotalQuantity());
    }

    /*Teste si l'erreur apparait en cas de quantité erronée'*/
    public function testDeleteFunctionalityQuantityError(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/supprimer-une-crypto');
        $cryptoMoneyRepository = static::getContainer()->get(CryptoMoneyRepository::class);
        $transactionRepository = static::getContainer()->get(TransactionRepository::class);
        $crypto = $cryptoMoneyRepository->findBy(['symbol' => "BTC"]);
        if ($crypto) {
            $crypto = $crypto[0];
            foreach ($crypto->getTransactions() as $transaction) {
                $transactionRepository->remove($transaction, true);
            }
        } else {
            $crypto = new CryptoMoney();
            $crypto->setSymbol('BTC')
                ->setLogoLink('XXXX')
                ->setTitle('Bitcoin');
        }

        $transaction = new Transaction();
        $transaction->setCreatedAt(new \DateTimeImmutable())
            ->setUnitPrice(10000)
            ->setQuantity(4)
            ->setType('purchase');
        $crypto->addTransaction($transaction);
        $cryptoMoneyRepository->save($crypto, true);
        $form = $crawler->selectButton('Supprimer')->form();
        $form['quantity'] = "abc";
        $form['crypto'] = 'BTC';
        $form['value'] = 1000;
        $form['unitPrice'] = 190000;
        $client->submit($form);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful($response);
        $this->assertSelectorExists('.alert-danger');
    }

    /*Teste si l'erreur apparait en cas de valeur erronée'*/
    public function testDeleteFunctionalityPriceError(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/supprimer-une-crypto');
        $cryptoMoneyRepository = static::getContainer()->get(CryptoMoneyRepository::class);
        $transactionRepository = static::getContainer()->get(TransactionRepository::class);
        $crypto = $cryptoMoneyRepository->findBy(['symbol' => "BTC"]);
        if ($crypto) {
            $crypto = $crypto[0];
            foreach ($crypto->getTransactions() as $transaction) {
                $transactionRepository->remove($transaction, true);
            }
        } else {
            $crypto = new CryptoMoney();
            $crypto->setSymbol('BTC')
                ->setLogoLink('XXXX')
                ->setTitle('Bitcoin');
        }

        $transaction = new Transaction();
        $transaction->setCreatedAt(new \DateTimeImmutable())
            ->setUnitPrice(10000)
            ->setQuantity(4)
            ->setType('purchase');
        $crypto->addTransaction($transaction);
        $cryptoMoneyRepository->save($crypto, true);
        $form = $crawler->selectButton('Supprimer')->form();
        $form['quantity'] = 10;
        $form['crypto'] = 'BTC';
        $form['value'] = 1000;
        $form['unitPrice'] = 'abc';
        $client->submit($form);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful($response);
        $this->assertSelectorExists('.alert-danger');
    }
}
