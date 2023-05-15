<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\DataSource;

use App\Entity\CurrencyRate;
use DateTimeImmutable;

/**
 * Interfejs który powinien być implementowany przez wszystkie źródła danych dotyczące kursów walut
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\DataSource
 */
interface DataSourceInterface
{
    /**
     * Ustawia datę publikacji kursów walut której mają dotyczyć zwracane encje
     *
     * @param DateTimeImmutable $date
     */
    public function setDate(DateTimeImmutable $date): void;

    /**
     * Zwraca encje CurrencyRate pasujące do wcześniej zdefiniowanych parametrów
     *
     * @return CurrencyRate[] array
     */
    public function getData(): array;
}
