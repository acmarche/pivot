<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\Filtre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Filtre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Filtre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Filtre[]    findAll()
 * @method Filtre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiltreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filtre::class);
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