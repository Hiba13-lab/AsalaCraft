<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Méthode personnalisée pour filtrer le catalogue par nom ou description
     */
    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :term OR p.description LIKE :term')
            ->setParameter('term', '%' . $term . '%') // Le % permet de trouver le mot n'importe où
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}