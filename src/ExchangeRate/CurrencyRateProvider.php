<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate;

use App\Entity\CurrencyRate;
use App\ExchangeRate\DataSource\DatabaseDataSource;
use App\ExchangeRate\DataSource\NbpApiDataSource;
use DateTimeImmutable;

/**
 * Wybiera odpowiednie źródło danych i dostarcza z niego encje CurrencyRate
 *
 * Wybrane źródło danych zależy od tego, czy dane które mają zostać uzyskane istnieją, już w bazie danych.
 * Wyższy priorytet jako źródło danych ma baza danych. W przypadku braku możliwości uzyskania danych z bazy, następuje
 * wybór źródła danych o niższym priorytecie, czyli API NBP.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate
 */
class CurrencyRateProvider
{
    public function __construct(
        private DatabaseDataSource $database_data_source,
        private NbpApiDataSource $nbp_api_data_source
    ) {

    }

    /**
     * Ustawia datę publikacji kursów walut której mają dotyczyć zwracane encje
     *
     * @param DateTimeImmutable $date
     */
    public function setDate(DateTimeImmutable $date): void
    {

    }

    /**
     * Zwraca encje CurrencyRate pasujące do wcześniej zdefiniowanych parametrów
     *
     * @return CurrencyRate[] array
     */
    public function getData(): array
    {

    }
}
