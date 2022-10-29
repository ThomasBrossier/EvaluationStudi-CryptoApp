<?php

namespace App\Tests\Command;

use App\Entity\CryptoMoney;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class SaveAmountCommandTest extends KernelTestCase
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

    public function testWithDatas(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $crypto = new CryptoMoney();
        $crypto->setTitle('Bitcoin')
            ->setLogoLink('https://s2.coinmarketcap.com/static/img/coins/64x64/1.png')
            ->setSymbol('BTC');
        $transaction = new Transaction();
        $transaction->setCreatedAt(new \DateTimeImmutable())
            ->setType('purchase')
            ->setQuantity(1000)
            ->setUnitPrice(50);
        $crypto->addTransaction($transaction);
        $repo = $this->entityManager->getRepository(CryptoMoney::class);
        $repo->save($crypto,true);
        $command = $application->find('app:saveAmount');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        /*// the output of the command in the console
        $output = $commandTester->getDisplay();*/
    }

    public function testWithoutDatas(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:saveAmount');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $result = $commandTester->getStatusCode();
        $this->assertEquals(1,$result);

        /*// the output of the command in the console
        $output = $commandTester->getDisplay();*/
    }



    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
