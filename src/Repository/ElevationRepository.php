<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\Elevation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Elevation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Elevation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Elevation[]    findAll()
 * @method Elevation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElevationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Elevation::class);
    }

    /**
     * @return Elevation[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQBL()
            ->orderBy('elevation.latitude', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('elevation');
    }

    public function insert(object $object): void
    {
        $this->persist($object);
        $this->flush();
    }

    public function persist(object $object): void
    {
        $this->_em->persist($object);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function remove(object $object): void
    {
        $this->_em->remove($object);
    }
}
