<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\tests\integration\Nbp;

use App\Nbp\Api;
use App\Nbp\Exception\ResponseException;
use App\Nbp\Exception\ValidateException;
use App\Nbp\Request\ExchangeRatesTableRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use DateTimeImmutable;

/**
 * Testy integracyjne dotyczące obsługi API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\tests\integration\Nbp
 */
class ApiFullTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testCorrectGetExchangeRatesFromTableWithDate()
    {
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-18/';
        $body = $this->getExampleData();
        $this->createMockHttpClientAndLoadToContainer($expected_url, 200, $body);
        /* Wykonywanie żądania i uzyskiwanie odpowiedzi */
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $request->setDate(new DateTimeImmutable('2023-04-18'));
        $api = $this->getApi();
        $api->execute($request);
        $response = $api->getResponse();
        /* Weryfikacja odpowiedzi */
        $this->assertTrue($response->isSuccess());
        $this->assertSame(200, $response->getStatus());
        $response_array = $response->getResponseArray();
        $this->assertSame(['table', 'no', 'effectiveDate', 'rates'], array_keys($response_array));
    }

    public function testWithBadRequestResponse()
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Nie można uzyskać treści odpowiedzi ponieważ serwer HTTP zwrócił kod błędu 400');
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-17/';
        $this->createMockHttpClientAndLoadToContainer($expected_url, 400, 'test');
        /* Wykonywanie żądania i uzyskiwanie odpowiedzi */
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $request->setDate(new DateTimeImmutable('2023-04-17'));
        $api = $this->getApi();
        $api->execute($request);
        $response = $api->getResponse();
        /* Weryfikacja odpowiedzi */
        $this->assertFalse($response->isSuccess());
        $this->assertSame(400, $response->getStatus());
        $response->getResponseArray();
    }

    public function testWithBadResponseBody()
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Przetworzenie odpowiedzi z API NBP nie powiodło się');
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-17/';
        $this->createMockHttpClientAndLoadToContainer($expected_url, 200, 'test');
        /* Wykonywanie żądania i uzyskiwanie odpowiedzi */
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $request->setDate(new DateTimeImmutable('2023-04-17'));
        $api = $this->getApi();
        $api->execute($request);
        $response = $api->getResponse();
        /* Weryfikacja odpowiedzi */
        $this->assertTrue($response->isSuccess());
        $this->assertSame(200, $response->getStatus());
        $response->getResponseArray();
    }

    public function testValidationResponse()
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API nie zawiera wymaganego klucza "rates"');
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-18/';
        $body = '[{"table":"A","no":"075/A/NBP/2023","effectiveDate":"2023-04-18"}]';
        $this->createMockHttpClientAndLoadToContainer($expected_url, 200, $body);
        /* Wykonywanie żądania i uzyskiwanie odpowiedzi */
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $request->setDate(new DateTimeImmutable('2023-04-18'));
        $api = $this->getApi();
        $api->execute($request);
        $api->getResponse();
    }

    /**
     * Przygotowuje imitację HttpClient a następnie ładuje ją do kontenera zależności
     *
     * @param string $expected_url Spodziewany adres URL żądania
     * @param int $http_code Kod HTTP który ma zostać zwrócony w odpowiedzi
     * @param string $body Treść która ma zostać zwrócona w odpowiedzi
     */
    private function createMockHttpClientAndLoadToContainer(string $expected_url, int $http_code, string $body)
    {
        $expected_requests = [
            function ($method, $url) use ($expected_url, $http_code, $body)
            {
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
     * Zwraca instancję Api z kontenera zależności
     *
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    private function getApi(): Api
    {
        $container = static::getContainer();
        return $container->get(Api::class);
    }

    /**
     * Zwraca przykładowe dane wcześniej przygotowane w pliku exchange_rates_table.json
     *
     * @return string
     */
    private function getExampleData(): string
    {
        return file_get_contents(__DIR__.'/../../data/exchange_rates_table.json');
    }
}
