<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<CurrencyRate>
 *
 * @method CurrencyRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRate[]    findAll()
 * @method CurrencyRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    public function save(CurrencyRate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Wyszukuje kursy walut na podstawie daty ich opublikowania i ładuje relację do tabeli currency
     *
     * Ładowanie danych z relacji do tabeli currency ma na celu ograniczenie liczby zapytań do bazy danych.
     *
     * @return CurrencyRate[] Encje CurrencyRate posortowane alfabetycznie według pełnej nazwy waluty
     */
    public function findByDateAndLoadRelation(DateTimeImmutable $date): array
    {
        $builder = $this->createQueryBuilder('cr')
            ->select('cr', 'c')
            ->leftJoin('cr.currency', 'c')
            ->where('cr.date = :date')
            ->setParameter('date', $date)
            ->orderBy('c.name', 'ASC');
        $query = $builder->getQuery();
        $query->execute();
        return $query->getResult();
    }
}
