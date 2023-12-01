<?php

namespace AcMarche\Pivot\Repository;

use Doctrine\ORM\EntityManagerInterface;

trait OrmCrudTrait
{
    /**
     * @var EntityManagerInterface $_em
     */
    protected $_em;

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