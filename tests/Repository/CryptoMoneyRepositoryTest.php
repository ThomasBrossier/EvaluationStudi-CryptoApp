<?php

namespace App\Tests\Repository;

use App\Entity\CryptoMoney;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CryptoMoneyRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    public function getEntity() :CryptoMoney {

        $crypto = new CryptoMoney();
        $crypto->setTitle('Bitcoin')
            ->setLogoLink('https://s2.coinmarketcap.com/static/img/coins/64x64/1.png')
            ->setSymbol('BTC');
        return $crypto;

    }


    public function testFindAll(): void
    {

        $crypto = $this->getEntity();
        $repo = $this->entityManager->getRepository(CryptoMoney::class);
        $repo->save($crypto,true);
        $cryptos = $repo->findAll();

        $this->assertCount(1,$cryptos);
        $repo->remove($crypto,true);
    }
    public function testOneBySymbol(): void
    {

        $crypto = $this->getEntity();
        $repo = $this->entityManager->getRepository(CryptoMoney::class);
        $repo->save($crypto,true);
        $cryptos = $repo->findOneBy(['symbol'=>'BTC']);
        $this->assertInstanceOf(CryptoMoney::class, $cryptos);
        $repo->remove($crypto,true);
    }
    public function testFindAllWithTransactions(): void
    {
        $crypto = $this->getEntity();
        $repo = $this->entityManager->getRepository(CryptoMoney::class);
        $repo->save($crypto,true);
        $cryptos = $repo->findAllWithTransactions();

        $this->assertCount(1,$cryptos);
        $repo->remove($crypto,true);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

}
