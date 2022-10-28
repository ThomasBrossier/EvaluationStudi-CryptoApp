<?php

namespace App\Tests\Repository;

use App\Entity\Result;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultRepositoryTest extends KernelTestCase
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
    public function getEntity() :Result {

        $result = new Result();
        $result->setCreatedAt(new \DateTimeImmutable())
            ->setAmount('200.58');
        return $result;

    }


    public function testFindAll(): void
    {
        $result = $this->getEntity();
        $repo = $this->entityManager->getRepository(Result::class);
        $repo->save($result,true);
        $results = $repo->findAll();
        $this->assertCount(1,$results);
        $repo->remove($result,true);
        $results = $repo->findAll();
        $this->assertCount(0,$results);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
