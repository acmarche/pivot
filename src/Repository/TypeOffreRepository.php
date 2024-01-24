<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entity\TypeOffre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method TypeOffre|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeOffre|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeOffre[]    findAll()
 * @method TypeOffre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeOffreRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    private TypeOffre $child;

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
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TypeOffre[]
     */
    public function findWithChildren(bool $removeFilterEmpty): array
    {
        $roots = $this->findRoots();
        $typesOffre = [];
        foreach ($roots as $root) {
            $root->children = $this->findByParent($root->id, $removeFilterEmpty);
            $typesOffre[] = $root;
        }

        return $typesOffre;
    }

    /**
     * @return TypeOffre[]
     */
    public function findByParent(int $id, bool $removeFilterEmpty = true): array
    {
        $qb = $this->createQBL()
            ->andWhere('typeOffre.parent = :id')
            ->setParameter('id', $id);

        if ($removeFilterEmpty) {
            $qb->andWhere('typeOffre.countOffres > 0');
        }

        return $qb->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TypeOffre[]
     */
    public function findByName(string $name, int $max = 20): array
    {
        return $this->createQBL()
            ->andWhere('typeOffre.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->setMaxResults($max)
            ->getResult();
    }

    /**
     * @return TypeOffre[]
     */
    public function findByNameOrUrn(string $name, int $max = 20): array
    {
        return $this->createQBL()
            ->andWhere('typeOffre.name LIKE :name OR typeOffre.urn LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->setMaxResults($max)
            ->getResult();
    }

    /**
     * @param array|string[] $typesOffreData
     * @return TypeOffre[]
     * @throws NonUniqueResultException
     */
    public function findByUrns(array $typesOffreData): array
    {
        $typesOffre = [];
        foreach ($typesOffreData as $typeOffreId) {
            try {
                if (($typeOffre = $this->findOneByUrn($typeOffreId)) instanceof TypeOffre) {
                    $typesOffre[] = $typeOffre;
                }
            } catch (Exception) {
            }
        }

        return $typesOffre;
    }

    /**
     * @return TypeOffre|null
     * @throws NonUniqueResultException
     */
    public function findOneByTypeId(int $id): ?TypeOffre
    {
        return $this->createQBL()
            ->andWhere('typeOffre.typeId = :id')
            ->setParameter('id', $id)
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByUrn(string $urn): ?TypeOffre
    {
        return $this->createQBL()
            ->andWhere('typeOffre.urn = :urn')
            ->setParameter('urn', $urn)
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return TypeOffre[]
     */
    public function findByUrn(string $urn): array
    {
        return $this->createQBL()
            ->andWhere('typeOffre.urn = :urn')
            ->setParameter('urn', $urn)
            ->orderBy('typeOffre.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TypeOffre[]
     */
    public function findByUrnLike(string $urn, bool $removeFilterEmpty = true): array
    {
        $qb = $this->createQBL()
            ->andWhere('typeOffre.urn LIKE :urn')
            ->setParameter('urn', $urn.'%');

        if ($removeFilterEmpty) {
            $qb->andWhere('typeOffre.countOffres > 0');
        }

        return $qb
            ->orderBy('typeOffre.name', 'ASC')->getQuery()
            ->getResult();
    }

    /**
     * Pour les urns donnes $typesOffre remonte sest parents pour avoir son type root
     * (type = Family)
     * Pour un pre tri avant un fetchOffre
     * @param array|TypeOffre[] $typesOffre
     * @return int[]
     */
    public function findFamiliesByUrns(array $typesOffre): array
    {
        $families = [];
        foreach ($typesOffre as $typeOffre) {
            $family = $typeOffre->type == 'Family' ? $typeOffre : $this->getFamily($typeOffre);;
            $families[$family->typeId] = $family->typeId;
        }

        return $families;
    }

    private function getFamily(TypeOffre $typeOffre): TypeOffre
    {
        while ($typeOffre->type != 'Family') {
            $this->child = $typeOffre;

            return $this->getFamily($typeOffre->parent);
        }

        return $this->child;
    }

    public function createQBL(): QueryBuilder
    {
        return $this->createQueryBuilder('typeOffre')
            ->leftJoin('typeOffre.parent', 'parent', 'WITH')
            ->addSelect('parent');
    }
}
