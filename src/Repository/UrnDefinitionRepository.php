<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\UrnDefinitionEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UrnDefinitionEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UrnDefinitionEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UrnDefinitionEntity[]    findAll()
 * @method UrnDefinitionEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrnDefinitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrnDefinitionEntity::class);
    }

    public function findByUrn(string $urn): ?UrnDefinitionEntity
    {
        return $this->createQBL()
            ->andWhere('urn_definition.urn = :urn')
            ->setParameter('urn', $urn)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('urn_definition');
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