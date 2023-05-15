<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Repository\CurrencyRepository;
use DateTimeImmutable;

/**
 * Tworzy encje CurrencyRate wraz z odpowiednią relacją do encji Currency i zapisuje ją w bazie danych
 *
 * Encja CurrencyRate posiada relację do encji Currency. W związku z tym, przed utworzeniem encji CurrencyRate konieczne
 * jest uzyskanie encji Currency. Jeżeli odpowiednia encja Currency nie istnieje jeszcze w bazie danych, następuje jej
 * utworzenie.
 *
 * Utworzone encje mogą zostać zapisane w bazie danych przez użycie metody save.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate
 */
class CurrencyRateCreator
{
    /**
     * @var Currency[] array
     */
    private array $currencies = [];
    /**
     * @var Currency[] array
     */
    private array $created_currencies = [];
    /**
     * @var CurrencyRate[] array
     */
    private array $created_currency_rates = [];

    public function __construct(
        private CurrencyRepository $currency_repository,
        private CurrencyRateRepository $currency_rate_repository
    ) {
        $this->loadExistCurrenciesFromDatabase();
    }

    /**
     * Tworzy encję CurrencyRate na podstawie przekazanych danych
     *
     * Utworzone encje nie są zapisywane w bazie danych. W związku z tym wymagane jest późniejsze użycie metody save.
     *
     * @param string $currency_name Nazwa waluty
     * @param string $currency_code Kod waluty
     * @param float $exchange_rate Kurs wymiany
     * @param DateTimeImmutable $date Data opublikowania kursu wymiany
     * @return CurrencyRate
     */
    public function create(
        string $currency_name,
        string $currency_code,
        float $exchange_rate,
        DateTimeImmutable $date
    ): CurrencyRate
    {
        $currency_code = strtoupper($currency_code);
        if (isset($this->currencies[$currency_code])) {
            $currency = $this->currencies[$currency_code];
        } else {
            $currency = new Currency($currency_code, $currency_name);
            $this->currencies[$currency_code] = $currency;
            $this->created_currencies[] = $currency;
        }
        $currency_rate = new CurrencyRate($currency, $exchange_rate, $date);
        $this->created_currency_rates[] = $currency_rate;
        return $currency_rate;
    }

    /**
     * Zapisuje utworzone encje w bazie danych
     *
     * Niniejsza metoda ma na celu zapis wszystkich wcześniej utworzonych encji w bazie danych. Dane zapisywane są w
     * ramach jednej transakcji i taki jest główny cel istnienia niniejszej metody.
     */
    public function save(): void
    {
        foreach ($this->created_currencies as $currency) {
            $this->currency_repository
                ->save($currency);
        }

        $last_key = array_key_last($this->created_currency_rates);
        foreach ($this->created_currency_rates as $key => $currency_rate) {
            if ($key !== $last_key) {
                $this->currency_rate_repository
                    ->save($currency_rate);
            } else {
                $this->currency_rate_repository
                    ->save($currency_rate, true); // Wywołanie zapisu encji w bazie danych
            }
        }
    }

    /**
     * Ładuje encje Currency już istniejące w bazie danych
     *
     * Encje ładowane są właściwości o nazwie "currencies" gdzie klucz tablicy to kod waluty.
     */
    private function loadExistCurrenciesFromDatabase(): void
    {
        $result = $this->currency_repository
            ->findAll();
        foreach ($result as $currency) {
            $code = $currency->getCode();
            $this->currencies[$code] = $currency;
        }
    }
}
