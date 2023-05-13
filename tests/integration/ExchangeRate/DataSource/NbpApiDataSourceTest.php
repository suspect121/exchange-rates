<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Tests\Integration\ExchangeRate\DataSource;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\ExchangeRate\DataSource\NbpApiDataSource;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpApiDataSourceTest extends KernelTestCase
{
    private EntityManager $entity_manager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entity_manager = static::getContainer()
            ->get('doctrine')
            ->getManager();
        $connection = $this->entity_manager
            ->getConnection();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $connection->executeStatement('DELETE FROM currency_rate');
        $connection->executeStatement('DELETE FROM currency');
    }

    public function testGetData(): void
    {
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-12/';
        $body = $this->getExampleData();
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

    /**
     * Przygotowuje imitację HttpClient a następnie ładuje ją do kontenera zależności
     *
     * @param string $expected_url Spodziewany adres URL żądania
     * @param int $http_code Kod HTTP który ma zostać zwrócony w odpowiedzi
     * @param string $body Treść która ma zostać zwrócona w odpowiedzi
     */
    private function createMockHttpClientAndLoadToContainer(string $expected_url, int $http_code, string $body): void
    {
        $expected_requests = [
            function ($method, $url) use ($expected_url, $http_code, $body) {
                $this->assertSame('GET', $method);
                $this->assertSame($expected_url, $url);
                return new MockResponse($body, ['http_code' => $http_code]);
            }
        ];
        $http_client = new MockHttpClient($expected_requests);
        static::getContainer()
            ->set(HttpClientInterface::class, $http_client);
    }

    /**
     * Zwraca przykładowe dane wcześniej przygotowane w pliku exchange_rates_table.json
     *
     * @return string
     */
    private function getExampleData(): string
    {
        return file_get_contents(__DIR__.'/../../../data/exchange_rates_table.json');
    }

    /**
     * Zwraca liczbę rekordów z tabeli której dotyczy encja Currency
     *
     * @return int
     */
    private function getCurrencyCount(): int
    {
        return $this->entity_manager
            ->getRepository(Currency::class)
            ->count([]);
    }

    /**
     * Zwraca liczbę rekordów z tabeli której dotyczy encja CurrencyRate
     *
     * @return int
     */
    private function getCurrencyRateCount(): int
    {
        return $this->entity_manager
            ->getRepository(CurrencyRate::class)
            ->count([]);
    }
}
