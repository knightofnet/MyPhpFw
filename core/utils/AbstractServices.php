<?php

namespace myphpfw\core\utils;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AbstractServices
{

    private EntityManager $entityManager;
    private string $entityClass;
    private EntityRepository $repo;

    public function __construct(EntityManager $em, string $entityClass)
    {
        $this->entityManager = $em;
        $this->entityClass = $entityClass;
        $this->repo = $this->entityManager->getRepository($entityClass);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return EntityRepository
     */
    public function getRepo(): EntityRepository
    {
        return $this->repo;
    }



}