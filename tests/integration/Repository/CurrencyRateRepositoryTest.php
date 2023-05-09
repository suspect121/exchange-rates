<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\tests\integration\Repository;

use App\DataFixtures\BasicFixtures;
use App\Repository\CurrencyRateRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class CurrencyRateRepositoryTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        $entity_manager = static::getContainer()
            ->get('doctrine')
            ->getManager();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $connection = $entity_manager->getConnection();
        $connection->executeStatement('DELETE FROM currency_rate');
        $connection->executeStatement('DELETE FROM currency');
        /* Ładowanie przykładowych danych */
        $basic_fixtures = new BasicFixtures();
        $basic_fixtures->load($entity_manager);
    }

    public function testFindByDateAndLoadRelation(): void
    {
        /** @var CurrencyRateRepository $currency_rate_repository */
        $currency_rate_repository = static::getContainer()
            ->get(CurrencyRateRepository::class);
        $date = new DateTimeImmutable('2023-04-18');
        $result = $currency_rate_repository->findByDateAndLoadRelation($date);
        $this->assertCount(4, $result);
        $this->assertSame('USD', $result[0]->getCurrency()->getCode());
        $this->assertSame(4.2151, $result[0]->getExchangeRate());
    }
}
