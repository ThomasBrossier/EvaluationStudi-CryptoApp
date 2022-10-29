<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function getentity(): User{
    $user = new User();
        $user->setEmail('test@test.fr')
            ->setPassword('test')
            ->setRoles(['ROLE_USER'])
        ;
        return $user;
    }
    public function testIsEmpty(){
        $user = new User();
        $this->assertEmpty($user->getId());
        $this->assertEmpty($user->getEmail()  );
    }
    public function testIsNotEmpty(){
        $user = $this->getentity();
        $this->assertEquals('test@test.fr', $user->getEmail());
        $this->assertEquals('test',$user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
    public function testEntityIsValid(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();

        $result = $this->getEntity();
        $errors = $container->get('validator')->validate($result);
        $this->assertCount(0,$errors);
    }
}
