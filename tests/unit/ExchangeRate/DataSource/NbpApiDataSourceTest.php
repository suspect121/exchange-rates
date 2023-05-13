<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\ExchangeRate\DataSource;

use App\Entity\CurrencyRate;
use App\ExchangeRate\CurrencyRateCreator;
use App\ExchangeRate\DataSource\NbpApiDataSource;
use App\ExchangeRate\Exception\DataSourceException;
use App\ExchangeRate\Exception\TodayNoDataException;
use App\Nbp\Api;
use App\Nbp\ApiResponse;
use App\Nbp\Request\ExchangeRatesTableRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class NbpApiDataSourceTest extends TestCase
{
    /**
     * Test mający na celu dokładne prześledzenie zachowania klasy podczas użycia metody getData
     */
    public function testGetData(): void
    {
        /* Przygotowanie imitacji ExchangeRatesTableRequest */
        $request_mock = $this->getMockBuilder(ExchangeRatesTableRequest::class)
            ->getMock();
        $request_mock->expects($this->once())
            ->method('setDate');
        $request_mock->expects($this->once())
            ->method('setTable')
            ->with('A');

        /* Przygotowanie imitacji ApiResponse z przykładowymi danymi */
        $example_data = $this->getExampleData();
        $response_mock = $this->getResponseMockWithStatus(200);
        $response_mock->expects($this->once())
            ->method('getResponseArray')
            ->willReturn($example_data);

        /* Przygotowanie imitacji Api która zwróci wcześniej przygotowaną imitację odpowiedzi */
        $api_mock = $this->getMockBuilder(Api::class)
            ->disableOriginalConstructor()
            ->getMock();
        $api_mock->expects($this->once())
            ->method('execute')
            ->with($request_mock);
        $api_mock->expects($this->once())
            ->method('getResponse')
            ->willReturn($response_mock);

        /* Przygotowanie imitacji CurrencyRateCreator */
        $creator_mock = $this->getMockBuilder(CurrencyRateCreator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $creator_mock->expects($this->exactly(33))
            ->method('create');
        $creator_mock->expects($this->once())
            ->method('save');

        /* Tworzenie instancji NbpApiDataSource z imitacjami */
        $data_source = new NbpApiDataSource($api_mock, $request_mock, $creator_mock);
        $data_source->setDate(new DateTimeImmutable('2023-04-12'));
        $data = $data_source->getData();

        $this->assertCount(33, $data);
        $this->assertInstanceOf(CurrencyRate::class, $data[0]);
    }

    /**
     * Test sprawdzający wystąpienie wyjątku "TodayNoDataException"
     */
    public function testTodayNoDataException()
    {
        $this->expectException(TodayNoDataException::class);
        $this->expectExceptionMessage('Brak danych - nie opublikowano kursów walut z dnia dzisiejszego');

        /* Przygotowanie imitacji ExchangeRatesTableRequest */
        $request_mock = $this->getMockBuilder(ExchangeRatesTableRequest::class)
            ->getMock();

        /* Przygotowanie imitacji ApiResponse */
        $response_mock = $this->getResponseMockWithStatus(404);

        /* Przygotowanie imitacji Api która zwróci wcześniej przygotowaną imitację odpowiedzi */
        $api_mock = $this->getMockBuilder(Api::class)
            ->disableOriginalConstructor()
            ->getMock();
        $api_mock->expects($this->once())
            ->method('getResponse')
            ->willReturn($response_mock);

        /* Przygotowanie imitacji CurrencyRateCreator */
        $creator_mock = $this->getMockBuilder(CurrencyRateCreator::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* Przygotowanie dzisiejszej daty bez czasu */
        $date = new DateTimeImmutable(date('Y-m-d'));

        /* Tworzenie instancji NbpApiDataSource z imitacjami */
        $data_source = new NbpApiDataSource($api_mock, $request_mock, $creator_mock);
        $data_source->setDate($date);
        $data_source->getData();
    }

    /**
     * Test użycia metody "getData" bez wcześniejszego ustawienia daty metodą "setDate"
     */
    public function testGetDataWithoutSetDate()
    {
        $this->expectException(DataSourceException::class);
        $this->expectExceptionMessage('Nie przekazano daty której mają dotyczyć zwracane kursy walut');

        /* Przygotowanie imitacji ExchangeRatesTableRequest */
        $request_mock = $this->getMockBuilder(ExchangeRatesTableRequest::class)
            ->getMock();

        /* Przygotowanie imitacji ApiResponse */
        $this->getMockBuilder(ApiResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* Przygotowanie imitacji Api która zwróci wcześniej przygotowaną imitację odpowiedzi */
        $api_mock = $this->getMockBuilder(Api::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* Przygotowanie imitacji CurrencyRateCreator */
        $creator_mock = $this->getMockBuilder(CurrencyRateCreator::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* Tworzenie instancji NbpApiDataSource z imitacjami */
        $data_source = new NbpApiDataSource($api_mock, $request_mock, $creator_mock);
        $data_source->getData();
    }

    /**
     * Zwraca przykładowe dane wcześniej przygotowane w pliku exchange_rates_table.json
     *
     * @return array
     */
    private function getExampleData(): array
    {
        $content = file_get_contents(__DIR__.'/../../../data/exchange_rates_table.json');
        return json_decode($content, true)[0];
    }

    /**
     * Zwraca imitację ApiResponse zwracającą wybrany status HTTP
     *
     * @param int $http_code
     * @return ApiResponse&MockObject
     */
    private function getResponseMockWithStatus(int $http_code): ApiResponse
    {
        $response_mock = $this->getMockBuilder(ApiResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response_mock->expects($this->once())
            ->method('getStatus')
            ->willReturn($http_code);
        return $response_mock;
    }
}
