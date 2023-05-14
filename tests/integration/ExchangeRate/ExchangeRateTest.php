<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Tests\Integration\ExchangeRate;

use App\ExchangeRate\ExchangeRate;
use App\Tests\Support\ClearDatabaseTrait;
use App\Tests\Support\EntityCountTrait;
use App\Tests\Support\FixturesTrait;
use App\Tests\Support\MockHttpClientTrait;
use App\Tests\Support\NbpExampleDataTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class ExchangeRateTest extends KernelTestCase
{
    use ClearDatabaseTrait;
    use EntityCountTrait;
    use FixturesTrait;
    use MockHttpClientTrait;
    use NbpExampleDataTrait;

    private static bool $cleared_database = false;

    protected function setUp(): void
    {
        self::bootKernel();
        /* Jednorazowe czyszczenie bazy danych przed wykonaniem testów */
        if (!self::$cleared_database) {
            $this->clearDatabase();
            self::$cleared_database = true;
        }
    }

    /**
     * Test uzyskiwania danych które nie istnieją w bazie danych w związku z czym zachodzi konieczność wykorzystania API
     */
    public function testGetExchangeRatesFromApi(): void
    {
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-18/';
        $data = $this->getExampleExchangeRatesTable();
        $this->createMockHttpClientAndLoadToContainer($expected_url, 200, $data);

        /* Uzyskiwanie kursów walut */
        $date = new DateTimeImmutable('2023-04-18');
        $exchange_rate = $this->getExchangeRate();
        $data = $exchange_rate->getExchangeRates($date);

        /* Weryfikacja uzyskanych danych */
        $this->assertCount(33, $data);
        $this->assertSame(['currency_name', 'currency_code', 'exchange_rate'], array_keys($data[0]));

        /* Weryfikacja stanu bazy danych */
        $this->assertSame(33, $this->getCurrencyCount());
        $this->assertSame(33, $this->getCurrencyRateCount());
    }

    /**
     * Test uzyskiwania danych które już istnieją w bazie danych w związku z czym nie ma konieczności wykorzystania API
     *
     * @depends testGetExchangeRatesFromApi
     */
    public function testGetExchangeRatesFromDatabase(): void
    {
        /* Przygotowanie pustej imitacji klienta HTTP */
        $this->createEmptyMockHttpClientAndLoadToContainer();

        /* Uzyskiwanie kursów walut */
        $date = new DateTimeImmutable('2023-04-18');
        $exchange_rate = $this->getExchangeRate();
        $data = $exchange_rate->getExchangeRates($date);

        /* Weryfikacja uzyskanych danych */
        $this->assertCount(33, $data);
    }

    /**
     * Zwraca instancję ExchangeRate z kontenera zależności
     *
     * @return ExchangeRate
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    private function getExchangeRate(): ExchangeRate
    {
        return self::getContainer()
            ->get(ExchangeRate::class);
    }
}
