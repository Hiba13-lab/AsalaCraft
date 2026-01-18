<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function countTotalOrders(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function calculateTotalRevenue(): float
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.total)')
            ->getQuery()
            ->getSingleScalarResult();
        return (float) ($result ?? 0);
    }

    /**
     * Récupère les revenus quotidiens des 7 derniers jours
     * Version optimisée pour MySQL/MariaDB
     */
    public function getSalesDataLast7Days(): array
    {
        $date = new \DateTime("-7 days");

        // Utilisation de SUBSTRING sur la propriété created_at
        // Note : On s'assure que le champ est traité comme une chaîne pour le groupement
        return $this->createQueryBuilder('o')
            ->select("SUBSTRING(o.created_at, 1, 10) as dateGroup, SUM(o.total) as total")
            ->where('o.created_at >= :date')
            ->setParameter('date', $date)
            ->groupBy('dateGroup')
            ->orderBy('dateGroup', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOrdersBySeller(User $sellerUser): array
    {
        return $this->createQueryBuilder('o')
            ->select('DISTINCT o')
            ->join('o.orderItems', 'oi') 
            ->join('oi.product', 'p')
            ->join('p.seller', 's') 
            ->join('s.user', 'u')
            ->where('u = :sellerUser') 
            ->setParameter('sellerUser', $sellerUser)
            ->orderBy('o.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}