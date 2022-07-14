<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\TypeOffre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeOffre|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeOffre|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeOffre[]    findAll()
 * @method TypeOffre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeOffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeOffre::class);
    }

    /**
     * @return TypeOffre[]
     */
    public function findRoots(): array
    {
        return $this->createQBL()
            ->andWhere('typeOffre.parent IS NULL')
            ->orderBy('typeOffre.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TypeOffre[]
     */
    public function findWithChildren(): array
    {
        $roots = $this->findRoots();
        $typesOffre = [];
        foreach ($roots as $root) {
            $root->children = $this->findByParent($root->id);
            $typesOffre[] = $root;
        }

        return $typesOffre;
    }

    /**
     * @param integer $id
     * @return TypeOffre[]
     */
    public function findByParent(int $id): array
    {
        return $this->createQBL()
            ->andWhere('typeOffre.parent = :id')
            ->setParameter('id', $id)
            ->orderBy('typeOffre.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     * @return TypeOffre[]
     */
    public function findByName(string $name): array
    {
        return $this->createQBL()
            ->andWhere('typeOffre.nom LIKE :nom')
            ->setParameter('nom', '%'.$name.'%')
            ->orderBy('typeOffre.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $params
     * @return TypeOffre[]
     */
    public function findByIdsOrUrns(array $typesOffreData): array
    {
        $typesOffre = [];
        foreach ($typesOffreData as $typeOffreId) {
            if ((int)$typeOffreId) {
                if ($typeOffre = $this->findByTypeId($typeOffreId)) {
                    $typesOffre[] = $typeOffre;
                }
            } else {
                if ($typeOffre = $this->findByUrn($typeOffreId)) {
                    $typesOffre[] = $typeOffre;
                }
            }
        }

        return $typesOffre;
    }

    /**
     * @param int $id
     * @return TypeOffre|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByTypeId(int $id): ?TypeOffre
    {
        return $this->createQBL()
            ->andWhere('typeOffre.typeId = :id')
            ->setParameter('id', $id)
            ->orderBy('typeOffre.nom', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUrn(?string $urn): ?TypeOffre
    {
        return $this->createQBL()
            ->andWhere('typeOffre.urn = :urn')
            ->setParameter('urn', $urn)
            ->orderBy('typeOffre.nom', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('typeOffre')
            ->leftJoin('typeOffre.parent', 'parent', 'WITH')
            ->addSelect('parent');
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