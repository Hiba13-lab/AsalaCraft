<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * FILTRE STRICT : Récupère uniquement les produits d'un vendeur précis
     */
    public function findBySellerId(int $sellerId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.seller = :val')
            ->setParameter('val', $sellerId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :term OR p.description LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}