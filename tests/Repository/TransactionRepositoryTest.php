<?php

namespace App\Tests\Repository;

use App\Entity\CryptoMoney;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionRepositoryTest extends KernelTestCase
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
    public function getEntity() : Transaction {
        $transaction = new Transaction();
        $crypto = new CryptoMoney();
        $crypto->setTitle('Bitcoin')
            ->setLogoLink('https://s2.coinmarketcap.com/static/img/coins/64x64/1.png')
            ->setSymbol('BTC');
        $transaction->setType('purchase')
            ->setCrypto($crypto)
            ->setQuantity(10)
            ->setUnitPrice(100)
            ->setCreatedAt(new \DateTimeImmutable());

        return $transaction;
    }



    public function testFindAll(): void
    {
        $transaction = $this->getEntity();
        $repo = $this->entityManager->getRepository(Transaction::class);
        $repo->save($transaction,true);
        $transactions = $repo->findAll();

        $this->assertCount(1,$transactions);
        $repo->remove($transaction,true);

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
