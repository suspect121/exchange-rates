<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\DataSource;

use App\Entity\CurrencyRate;
use App\ExchangeRate\CurrencyRateCreator;
use App\ExchangeRate\Exception\DataSourceException;
use App\ExchangeRate\Exception\NoDataException;
use App\ExchangeRate\Exception\TodayNoDataException;
use App\Nbp\Api;
use App\Nbp\ApiResponse;
use App\Nbp\Exception\ApiException;
use App\Nbp\Exception\BadArgumentException;
use App\Nbp\Exception\ResponseException;
use App\Nbp\Request\ApiRequestInterface;
use App\Nbp\Request\ExchangeRatesTableRequest;
use Exception;
use DateTimeImmutable;

/**
 * Umożliwia uzyskanie danych na temat kursów walut z API NBP w formie encji
 *
 * Przed zwróceniem encji, są one zapisywane w bazie danych w celu ich ponownego użycia.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\DataSource
 */
class NbpApiDataSource implements DataSourceInterface
{
    private DateTimeImmutable $date;

    public function __construct(
        private Api $api,
        private ExchangeRatesTableRequest $exchange_rates_table_request,
        private CurrencyRateCreator $currency_rate_creator
    ) {

    }

    /**
     * @inheritDoc
     */
    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @inheritDoc
     *
     * @throws DataSourceException
     */
    public function getData(): array
    {
        /* Przepisywanie właściwości do lokalnych zmiennych */
        $request = $this->exchange_rates_table_request;
        /* Sprawdzanie czy data została ustawiona */
        $this->checkSetDate();
        /* Przygotowanie i wykonanie żądania */
        $request->setDate($this->date);
        try {
            $request->setTable('A');
        } catch (BadArgumentException $exception) {
            throw new DataSourceException('Błędna konstrukcja żądania - '.$exception->getMessage());
        }
        $response = $this->executeRequest($this->exchange_rates_table_request);
        /* Tworzenie encji na podstawie odpowiedzi i ich zapis */
        return $this->createAndSaveCurrencyRate($response);
    }

    /**
     * Sprawdza czy ustawiono datę której mają dotyczyć zwracane kursy walut
     *
     * @throws DataSourceException
     */
    private function checkSetDate(): void
    {
        if (!isset($this->date)) {
            throw new DataSourceException('Nie przekazano daty której mają dotyczyć zwracane kursy walut');
        }
    }

    /**
     * Wykonuje żądanie i zwraca odpowiedź w formie tablicy
     *
     * @param ApiRequestInterface $api_request Żądanie które ma zostać wykonane
     * @return array Odpowiedź z API NBP w formie tablicy
     * @throws DataSourceException
     */
    private function executeRequest(ApiRequestInterface $api_request): array
    {
        try {
            $this->api
                ->execute($api_request);
        } catch (ApiException $exception) {
            throw new DataSourceException('Wystąpił błąd podczas wykonywania żądania - '.$exception->getMessage());
        }
        return $this->getResponse($this->api);
    }

    /**
     * Zwraca odpowiedź z instancji Api
     *
     * Niniejsza metoda oprócz zwrócenia odpowiedzi ma zadanie odpowiednie przetworzenie wyjątków pochodzących z
     * wykorzystania Api.
     *
     * @param Api $api Instancja API w której zostało już wykonane żądanie
     * @return array Odpowiedź z API NBP w formie tablicy
     * @throws DataSourceException
     */
    private function getResponse(Api $api): array
    {
        try {
            $response = $api->getResponse();
            $this->checkResponseStatus($response);
            /* Uzyskiwanie odpowiedzi w formie tablicy lub zwracanie odpowiedniego wyjątku */
            $response = $response->getResponseArray();
        } catch (ResponseException) {
            throw new DataSourceException('Wystąpił błąd podczas przetwarzania odpowiedzi z API');
        } catch (ApiException $exception) {
            throw new DataSourceException('Wystąpił błąd podczas wykorzystania API - '.$exception->getMessage());
        }
        return $response;
    }

    /**
     * Sprawdza uzyskaną odpowiedź pod kątem ewentualnych błędów sygnalizowanych statusem zwróconym przez serwer HTTP
     *
     * Jeżeli w wyniku analizy statusu HTTP zostanie odnaleziony błąd, zwracany jest odpowiedni wyjątek.
     *
     * @param ApiResponse $api_response Odpowiedź uzyskana z API NBP w formie instancji
     * @throws DataSourceException
     */
    private function checkResponseStatus(ApiResponse $api_response): void
    {
        $status = $api_response->getStatus();
        /* Zwracanie odpowiedniego wyjątku w przypadku błędu oznaczającego brak danych */
        if ($status === 404) {
            $today_date = $this->getTodayDate();
            if ($this->date == $today_date) {
                throw new TodayNoDataException('Brak danych - nie opublikowano kursów walut z dnia dzisiejszego');
            }
            throw new NoDataException('Brak danych - nie opublikowano kursów walut z wybranego dnia');
        }
        /* Zwracanie odpowiedniego wyjątku w przypadku pozostałych błędów HTTP */
        if ($status !== 200) {
            throw new DataSourceException('Wystąpił nieoczekiwany błąd podczas uzyskiwania danych - HTTP '.$status);
        }
    }

    /**
     * Zwraca dzisiejszą datę bez zdefiniowanego czasu
     *
     * @return DateTimeImmutable
     * @throws Exception
     */
    private function getTodayDate(): DateTimeImmutable
    {
        $date = date('Y-m-d');
        return new DateTimeImmutable($date);
    }

    /**
     * Tworzy i zapisuje encje CurrencyRate na podstawie przekazanej odpowiedzi z API
     *
     * @param array $response Odpowiedź z API NBP w formie tablicy
     * @return CurrencyRate[] array
     */
    private function createAndSaveCurrencyRate(array $response): array
    {
        $creator = $this->currency_rate_creator;
        $currency_rates = [];
        foreach ($response['rates'] as $currency_rate) {
            $currency_rates[] = $creator->create($currency_rate['currency'], $currency_rate['code'], $currency_rate['mid'], $this->date);
        }
        $creator->save();
        return $currency_rates;
    }
}
