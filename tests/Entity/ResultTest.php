<?php

namespace App\Tests\Entity;

use App\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultTest extends KernelTestCase
{
    public function getentity(): Result
    {
        $result = new Result();
        $result->setCreatedAt(new \DateTimeImmutable('2022-10-28 14:42:26'))
            ->setAmount(200.58)
            ;
        return $result;
    }
    public function testIsEmpty(){
        $result = new Result();
        $this->assertEmpty($result->getId());
        $this->assertEmpty($result->getAmount());
        $this->assertEmpty($result->getCreatedAt());
    }
    public function testEntityIsValid(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $result = $this->getEntity();
        $errors = $container->get('validator')->validate($result);
        $this->assertCount(0,$errors);
    }
    public function testResultCreate(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $result = $this->getentity();
        $this->assertEquals(200.58,$result->getAmount());
        $this->assertEquals(new \DateTimeImmutable('2022-10-28 14:42:26'),$result->getCreatedAt());
    }
}
