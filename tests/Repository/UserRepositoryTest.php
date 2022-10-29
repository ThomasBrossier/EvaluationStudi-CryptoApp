<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
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
    public function getentity(): User{
        $user = new User();
        $user->setEmail('test@test.com')
            ->setPassword('test')
            ->setRoles(['ROLE_USER'])
        ;
        return $user;
    }



    public function testFindAll(): void
    {
        $user = $this->getEntity();
        $repo = $this->entityManager->getRepository(User::class);
        $repo->save($user, true);
        $users = $repo->findAll();

        $this->assertCount(2, $users);
        $repo->remove($user, true);

    }



    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
