<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\UrnDefinitionEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method UrnDefinitionEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UrnDefinitionEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UrnDefinitionEntity[]    findAll()
 * @method UrnDefinitionEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrnDefinitionRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrnDefinitionEntity::class);
    }

    public function findByUrn(string $urn): ?UrnDefinitionEntity
    {
        try {
            return $this->createQBL()
                ->andWhere('urn_definition.urn = :urn')
                ->setParameter('urn', $urn)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (Exception) {
            return null;
        }
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('urn_definition');
    }
}
