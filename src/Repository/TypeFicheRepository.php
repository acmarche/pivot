<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\TypeFiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeFiche|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeFiche|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeFiche[]    findAll()
 * @method TypeFiche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeFicheRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeFiche::class);
    }

    /**
     * @return TypeFiche[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQBL()
            ->orderBy('type_fiche.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TypeFiche[]
     */
    public function findByCode(string $code): array
    {
        return $this->createQBL()
            ->andWhere('type_fiche.name = :name')
            ->setParameter('name', $code)
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('type_fiche');
    }

}
