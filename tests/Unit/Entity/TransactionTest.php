<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionTest extends KernelTestCase
{

    public function getTransaction() : Transaction {
        $transaction = new Transaction();
        $crypto = CryptoMoneyTest::class->getEntity();
        $transaction->setType('purchase')
            ->setCrypto($crypto)
            ->setQuantity(10)
            ->setUnitPrice(100)
            ->setCreatedAt(new \DateTimeImmutable());

        return $transaction;
    }

    public function testIsEmpty(){
        $transaction = new Transaction();
        $this->assertEmpty($transaction->getId());
        $this->assertEmpty($transaction->getCrypto());
        $this->assertEmpty($transaction->getType());
        $this->assertEmpty($transaction->getCreatedAt());
        $this->assertEmpty($transaction->getQuantity());
        $this->assertEmpty($transaction->getUnitPrice());
    }
}
