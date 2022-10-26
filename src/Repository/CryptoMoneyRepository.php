<?php

namespace App\Repository;

use App\Entity\CryptoMoney;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CryptoMoney>
 *
 * @method CryptoMoney|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryptoMoney|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryptoMoney[]    findAll()
 * @method CryptoMoney[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryptoMoneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptoMoney::class);
    }

    public function save(CryptoMoney $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CryptoMoney $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return CryptoMoney[] Returns an array of CryptoMoney objects
     */
    public function findAllWithTransactions(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.transactions','t') ->addSelect('t')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?CryptoMoney
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
