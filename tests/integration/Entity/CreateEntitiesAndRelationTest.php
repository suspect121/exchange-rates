<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Integration\Entity;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Tests\Support\ClearDatabaseTrait;
use App\Tests\Support\EntityManagerTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class CreateEntitiesAndRelationTest extends KernelTestCase
{
    use ClearDatabaseTrait;
    use EntityManagerTrait;

    private static bool $cleared_database = false;

    protected function setUp(): void
    {
        self::bootKernel();
        /* Jednorazowe czyszczenie tabel przed rozpoczęciem wszystkich testów */
        if (!self::$cleared_database) {
            $this->clearDatabase();
            self::$cleared_database = true;
        }
    }

    public function testCreateAndSaveEntities(): void
    {
        $this->expectNotToPerformAssertions();

        /* Przygotowanie encji */
        $currency = new Currency('EUR', 'euro');
        $date = new DateTimeImmutable();
        $date->setDate(2023, 5, 8);
        $currency_rate = new CurrencyRate($currency, 1.25, $date);

        /* Zapis utworzonych encji w bazie danych */
        $em = $this->getEntityManager();
        $em->persist($currency);
        $em->persist($currency_rate);
        $em->flush();
    }

    /**
     * @depends testCreateAndSaveEntities
     */
    public function testCurrencyRateToCurrencyRelation(): void
    {
        $currency_rate_repository = static::getContainer()
            ->get(CurrencyRateRepository::class);
        $current_rate = $currency_rate_repository->findOneBy([]);
        $currency = $current_rate->getCurrency();
        $this->assertSame('EUR', $currency->getCode());
    }
}
