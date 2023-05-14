<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\ExchangeRate;

use App\Entity\CurrencyRate;
use App\ExchangeRate\CurrencyRateProvider;
use App\ExchangeRate\DataSource\DatabaseDataSource;
use App\ExchangeRate\DataSource\NbpApiDataSource;
use App\ExchangeRate\Exception\NoRequiredParametersException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class CurrencyRateProviderTest extends TestCase
{
    /**
     * Test uzyskiwania danych z bazy
     */
    public function testGetDataFromDatabase(): void
    {
        $date = new DateTimeImmutable('2023-04-12');
        $currency_rate = $this->getMockCurrencyRate();

        /* Tworzenie imitacji DatabaseDataSource z oczekiwaniem uzyskania danych */
        $database_data_source = $this->getMockDatabaseDataSource();
        $database_data_source->expects($this->once())
            ->method('setDate')
            ->with($date);
        $database_data_source->expects($this->once())
            ->method('getData')
            ->willReturn([$currency_rate]);

        /* Tworzenie imitacji NbpApiDataSource z oczekiwaniem, że dane z tego źródła nie będą uzyskiwane */
        $api_data_source = $this->getMockApiDataSource();
        $api_data_source->expects($this->never())
            ->method('getData');

        /* Tworzenie instancji na podstawie wcześniej przygotowanych imitacji i uzyskanie danych */
        $provider = new CurrencyRateProvider($database_data_source, $api_data_source);
        $provider->setDate($date);
        $data = $provider->getData();

        /* Weryfikacja uzyskanych danych */
        $this->assertCount(1, $data);
        $this->assertInstanceOf(CurrencyRate::class, $data[0]);
    }

    /**
     * Test uzyskiwania danych z API NBP
     */
    public function testGetDataFromNbpApi(): void
    {
        $date = new DateTimeImmutable('2023-04-12');
        $currency_rate = $this->getMockCurrencyRate();

        /* Tworzenie imitacji DatabaseDataSource która nie zwróci żadnych danych */
        $database_data_source = $this->getMockDatabaseDataSource();
        $database_data_source->expects($this->once())
            ->method('setDate')
            ->with($date);
        $database_data_source->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        /* Tworzenie imitacji NbpApiDataSource z oczekiwaniem, że dane z tego źródła nie będą uzyskiwane */
        $api_data_source = $this->getMockApiDataSource();
        $api_data_source->expects($this->once())
            ->method('setDate')
            ->with($date);
        $api_data_source->expects($this->once())
            ->method('getData')
            ->willReturn([$currency_rate]);

        /* Tworzenie instancji na podstawie wcześniej przygotowanych imitacji i uzyskanie danych */
        $provider = new CurrencyRateProvider($database_data_source, $api_data_source);
        $provider->setDate($date);
        $data = $provider->getData();

        /* Weryfikacja uzyskanych danych */
        $this->assertCount(1, $data);
        $this->assertInstanceOf(CurrencyRate::class, $data[0]);
    }

    /**
     * Test uzyskiwania danych bez wcześniejszego przekazania daty której mają dotyczyć kursy walut
     */
    public function testGetDataWithoutSetDate()
    {
        $this->expectException(NoRequiredParametersException::class);
        $this->expectExceptionMessage('Nie przekazano daty której mają dotyczyć zwracane kursy walut');

        /* Tworzenie pustych imitacji źródeł danych */
        $database_data_source = $this->getMockDatabaseDataSource();
        $api_data_source = $this->getMockApiDataSource();

        /* Tworzenie instancji na podstawie wcześniej przygotowanych imitacji i uzyskanie danych */
        $provider = new CurrencyRateProvider($database_data_source, $api_data_source);
        $provider->getData();
    }

    /**
     *
     *
     * @return DatabaseDataSource&MockObject
     */
    private function getMockDatabaseDataSource(): DatabaseDataSource
    {
        return $this->getMockBuilder(DatabaseDataSource::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     *
     *
     * @return NbpApiDataSource&MockObject
     */
    private function getMockApiDataSource(): NbpApiDataSource
    {
        return $this->getMockBuilder(NbpApiDataSource::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Zwraca imitację CurrencyRate
     *
     * @return CurrencyRate&MockObject
     */
    private function getMockCurrencyRate(): CurrencyRate
    {
        return $this->getMockBuilder(CurrencyRate::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
