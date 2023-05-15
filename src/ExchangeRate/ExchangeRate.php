<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate;

use App\Entity\CurrencyRate;
use App\ExchangeRate\Exception\ExchangeRateException;
use DateTimeImmutable;

/**
 * Umożliwia uzyskanie informacji na temat kursów walut w relacji do PLN w formie gotowej do prezentacji użytkownikowi
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate
 */
class ExchangeRate
{
    public function __construct(private CurrencyRateProvider $currency_rate_provider)
    {

    }

    /**
     * Zwraca kursy walut w relacji do PLN w formie gotowej do prezentacji użytkownikowi
     *
     * @param DateTimeImmutable|null $date Data opublikowania kursów walut. W przypadku pominięcia tego argumentu,
     *                                      zwrócone będą dzisiejsze kursy walut.
     * @return array Kursy walut w formie tablicy gdzie każdy element reprezentuje jedną walutę wraz z jej kursem.
     *               Każdy z elementów tablicy posiada następujące klucze:
     *               <div><b>currency_name</b> - Pełna nazwa waluty</div>
     *               <div><b>currency_code</b> - Kod waluty</div>
     *               <div><b>exchange_rate</b> - Kurs wymiany w relacji do PLN</div>
     * @throws ExchangeRateException
     */
    public function getExchangeRates(DateTimeImmutable $date = null): array
    {
        if ($date === null) {
            $date = new DateTimeImmutable(date('Y-m-d'));
        }
        $currency_rates = $this->getCurrencyRates($date);
        return $this->getDataFromEntities($currency_rates);
    }

    /**
     * Zwraca encje CurrencyRate uzyskane z providera
     *
     * @param DateTimeImmutable $date
     * @return CurrencyRate[]
     * @throws ExchangeRateException
     */
    private function getCurrencyRates(DateTimeImmutable $date): array
    {
        $provider = $this->currency_rate_provider;
        $provider->setDate($date);
        return $provider->getData();
    }

    /**
     * Uzyskuje dane z encji CurrencyRate i zwraca je w formie tablicy
     *
     * @param CurrencyRate[] $currency_rates
     * @return array Zwracane dane to nazwa waluty, jej kod oraz kurs
     */
    private function getDataFromEntities(array $currency_rates): array
    {
        $processed_data = [];
        foreach ($currency_rates as $currency_rate) {
            $currency = $currency_rate->getCurrency();
            $processed_data[] = [
                'currency_name' => $currency->getName(),
                'currency_code' => $currency->getCode(),
                'exchange_rate' => $currency_rate->getExchangeRate()
            ];
        }
        return $processed_data;
    }
}
