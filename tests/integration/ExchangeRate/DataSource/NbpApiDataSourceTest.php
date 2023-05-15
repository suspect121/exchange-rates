<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Tests\Integration\ExchangeRate\DataSource;

use App\Entity\CurrencyRate;
use App\ExchangeRate\DataSource\NbpApiDataSource;
use App\Tests\Support\ClearDatabaseTrait;
use App\Tests\Support\EntityCountTrait;
use App\Tests\Support\EntityManagerTrait;
use App\Tests\Support\MockHttpClientTrait;
use App\Tests\Support\NbpExampleDataTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class NbpApiDataSourceTest extends KernelTestCase
{
    use ClearDatabaseTrait;
    use EntityCountTrait;
    use EntityManagerTrait;
    use MockHttpClientTrait;
    use NbpExampleDataTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $this->clearDatabase();
    }

    public function testGetData(): void
    {
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-12/';
        $body = $this->getExampleExchangeRatesTable();
        $this->createMockHttpClientAndLoadToContainer($expected_url, 200, $body);

        /* Uzyskiwanie danych */
        $data_source = $this->getNbpApiDataSource();
        $data_source->setDate(new DateTimeImmutable('2023-04-12'));
        $currency_rates = $data_source->getData();

        /* Weryfikacja zwróconych encji */
        $this->assertCount(33, $currency_rates);
        $this->assertInstanceOf(CurrencyRate::class, $currency_rates[0]);

        /* Weryfikacja ilości rekordów w tabelach */
        $this->assertSame(33, $this->getCurrencyCount());
        $this->assertSame(33, $this->getCurrencyRateCount());
    }

    /**
     * Zwraca instancję NbpApiDataSource z kontenera zależności
     *
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    private function getNbpApiDataSource(): NbpApiDataSource
    {
        return self::getContainer()
            ->get(NbpApiDataSource::class);
    }
}
