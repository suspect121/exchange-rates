<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Integration\Entity;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class CreateEntitiesAndRelationTest extends KernelTestCase
{
    private EntityManager $entity_manager;

    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
        $connection = static::getContainer()
            ->get('doctrine')
            ->getManager()
            ->getConnection();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $connection->executeStatement('DELETE FROM currency_rate');
        $connection->executeStatement('DELETE FROM currency');
    }

    protected function setUp(): void
    {
        $this->entity_manager = $this->getContainer()
            ->get('doctrine')
            ->getManager();
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
        $em = $this->entity_manager;
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
