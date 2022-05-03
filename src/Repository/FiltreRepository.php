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

    /**
     * @return Filtre[]
     */
    public function findRoots(): array
    {
        return $this->createQueryBuilder('filtre')
            ->andWhere('filtre.parent = 0')
            ->orderBy('filtre.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Filtre[]
     */
    public function findWithChildren(): array
    {
        $roots = $this->findRoots();
        $filtres = [];
        foreach ($roots as $root) {
            $root->children = $this->findByParent($root->reference);
            $filtres[] = $root;
        }

        return $filtres;
    }

    /**
     * @param integer $id
     * @return Filtre[]
     */
    public function findByParent(int $id): array
    {
        return $this->createQueryBuilder('filtre')
            ->andWhere('filtre.parent = :id')
            ->setParameter('id', $id)
            ->orderBy('filtre.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param integer[] $ids
     * @return Filtre[]
     */
    public function findByReferences(array $ids): array
    {
        return $this->createQueryBuilder('filtre')
            ->andWhere('filtre.reference IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('filtre.nom', 'ASC')
            ->getQuery()
            ->getResult();
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