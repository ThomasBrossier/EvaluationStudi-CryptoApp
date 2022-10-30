<?php

namespace App\Repository;

use App\Entity\CryptoMoney;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Cette classe permet de faire le lien entre l'entité CryptoMoney et la base de données
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

    /**
     * Sauvegarde une crypto donnée dans la base de données
     * @param CryptoMoney $entity
     * @param bool $flush
     * @return void
     */
    public function save(CryptoMoney $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une crypto de la base de donnée. (ATTENTION l'application en l'état ne supprime jamais une crypto. )
     * @param CryptoMoney $entity
     * @param bool $flush
     * @return void
     */
    public function remove(CryptoMoney $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Permet de renvoyer la crypto avec toutes ses transactions pour optimiser les requêtes à la base de données.
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
}
