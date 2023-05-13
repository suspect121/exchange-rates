<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Integration\Nbp;

use App\Nbp\Api;
use App\Nbp\Exception\ResponseException;
use App\Nbp\Exception\ValidateException;
use App\Nbp\Request\ExchangeRatesTableRequest;
use App\Tests\Support\MockHttpClientTrait;
use App\Tests\Support\NbpExampleDataTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

/**
 * Testy integracyjne dotyczące obsługi API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Tests\Integration\Nbp
 */
class ApiFullTest extends KernelTestCase
{
    use MockHttpClientTrait;
    use NbpExampleDataTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testCorrectGetExchangeRatesFromTableWithDate(): void
    {
        /* Przygotowanie imitacji odpowiedzi z serwera HTTP */
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-18/';
        $body = $this->getExampleExchangeRatesTable();
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

    public function testWithBadRequestResponse(): void
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

    public function testWithBadResponseBody(): void
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

    public function testValidationResponse(): void
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
     * Zwraca instancję Api z kontenera zależności
     *
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    private function getApi(): Api
    {
        $container = static::getContainer();
        return $container->get(Api::class);
    }
}
