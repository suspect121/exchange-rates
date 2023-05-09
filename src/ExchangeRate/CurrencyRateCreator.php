<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate;

use App\Entity\CurrencyRate;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;

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
    public function __construct(private EntityManager $entity_manager)
    {

    }

    /**
     * Tworzy encję CurrencyRate na podstawie przekazanych danych
     *
     * Utworzone encje nie są zapisywane w bazie danych. W związku z tym, wymagane jest późniejsze użycie metody save.
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

    }

    /**
     * Zapisuje utworzone encje w bazie danych
     *
     * Niniejsza metoda ma na celu zapis wszystkich wcześniej zwróconych encji w bazie danych. Dane zapisywane są w
     * ramach jednej transakcji i taki jest główny cel istnienia niniejszej metody.
     */
    public function save(): void
    {

    }
}
