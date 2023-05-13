<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Tests\Support;

use App\Entity\Currency;
use App\Entity\CurrencyRate;

trait EntityCountTrait
{
    use EntityManagerTrait;

    /**
     * Zwraca liczbę rekordów z tabeli której dotyczy encja Currency
     *
     * @return int
     */
    private function getCurrencyCount(): int
    {
        return $this->getEntityManager()
            ->getRepository(Currency::class)
            ->count([]);
    }

    /**
     * Zwraca liczbę rekordów z tabeli której dotyczy encja CurrencyRate
     *
     * @return int
     */
    private function getCurrencyRateCount(): int
    {
        return $this->getEntityManager()
            ->getRepository(CurrencyRate::class)
            ->count([]);
    }
}
