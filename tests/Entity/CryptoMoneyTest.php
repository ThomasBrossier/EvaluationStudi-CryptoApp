<?php

namespace App\Tests\Entity;

use App\Entity\CryptoMoney;
use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CryptoMoneyTest extends KernelTestCase
{
    public function getEntity() :CryptoMoney {

        $crypto = new CryptoMoney();
        $crypto->setTitle('Bitcoin')
            ->setLogoLink('https://s2.coinmarketcap.com/static/img/coins/64x64/1.png')
            ->setSymbol('BTC');
        for($i = 1 ; $i <= 3 ; $i++ ){
            $transaction = new Transaction();
            $transaction->setCreatedAt(new \DateTimeImmutable())
                ->setUnitPrice( 10000)
                ->setQuantity( 5)
                ->setCrypto($crypto);
            if($i % 2 === 0){
                $transaction->setType('sale');
            }else{
                $transaction->setType('purchase');
            }
            $crypto->addTransaction($transaction);
        }

        return $crypto;

    }
    public function testEntityIsValid(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $crypto = $this->getEntity();
        $errors = $container->get('validator')->validate($crypto);
        $this->assertCount(0,$errors);
    }
    public function testValidTitle(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $crypto = $this->getEntity();
        $crypto->setTitle('');

        $errors = $container->get('validator')->validate($crypto);
        $this->assertCount(2,$errors);
    }

    public function testGetTotalQuantity(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $crypto = $this->getEntity();
        $total = $crypto->getTotalQuantity();
        $this->assertEquals(5,$total);
    }
    public function testGetTotalSpent(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $crypto = $this->getEntity();
        $totalSpent = $crypto->getTotalSpent();
        $this->assertEquals(50000,$totalSpent);
    }
    public function testIsEmpty(){
        $crypto = new CryptoMoney();
        $this->assertEmpty($crypto->getId());
        $this->assertEmpty($crypto->getTitle());
        $this->assertEmpty($crypto->getLogoLink());
        $this->assertEmpty($crypto->getSymbol());
        $this->assertEmpty($crypto->getTransactions());
    }
    public function testRemoveTransaction(){
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $crypto = $this->getEntity();
        $transaction = $crypto->getTransactions()[0];
        $crypto->removeTransaction($transaction);
        $this->assertCount(2,$crypto->getTransactions());
    }
}
