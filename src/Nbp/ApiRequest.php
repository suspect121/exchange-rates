<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use DateTimeImmutable;

/**
 * Reprezentuje żądanie do API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class ApiRequest
{
    /**
     * Ustawia tabelę kursów walut której ma dotyczyć żądanie
     *
     * @param string $table
     */
    public function setTable(string $table): void
    {

    }

    /**
     * Ustawia datę dotyczącą przygotowywanego żądania
     *
     * @param DateTimeImmutable $date
     */
    public function setDate(DateTimeImmutable $date): void
    {

    }

    /**
     * Zwraca w pełni przygotowany adres URL żądania do API NBP
     *
     * @return string
     */
    public function getUrl(): string
    {

    }
}
