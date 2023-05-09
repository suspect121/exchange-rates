<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate;

/**
 * Umożliwia uzyskanie informacji na temat kursów walut w relacji do PLN w formie gotowej do prezentacji użytkownikowi
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate
 */
class ExchangeRate
{
    /**
     * Zwraca kursy walut w relacji do PLN w formie gotowej do prezentacji użytkownikowi
     *
     * @param \DateTimeImmutable|null $date Data opublikowania kursów walut. W przypadku pominięcia tego argumentu,
     *                                      zwrócone będą dzisiejsze kursy walut.
     * @return array Kursy walut w formie tablicy gdzie każdy element reprezentuje jedną walutę wraz z jej kursem.
     *               Każdy z elementów tablicy posiada następujące klucze:
     *               <div><b>currency_name</b> - Pełna nazwa waluty</div>
     *               <div><b>currency_code</b> - Kod waluty</div>
     *               <div><b>exchange_rate</b> - Kurs wymiany w relacji do PLN</div>
     */
    public function getExchangeRates(\DateTimeImmutable $date = null): array
    {

    }
}
