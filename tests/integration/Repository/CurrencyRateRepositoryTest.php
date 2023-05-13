<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Integration\Repository;

use App\Repository\CurrencyRateRepository;
use App\Tests\Support\ClearDatabaseTrait;
use App\Tests\Support\EntityManagerTrait;
use App\Tests\Support\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class CurrencyRateRepositoryTest extends KernelTestCase
{
    use ClearDatabaseTrait;
    use EntityManagerTrait;
    use FixturesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $this->clearDatabase();
        /* Ładowanie przykładowych danych */
        $this->loadBasicFixtures();
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
