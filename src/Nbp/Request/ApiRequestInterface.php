<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Request;

use App\Nbp\Exception\IncompleteRequestException;

/**
 * Interfejs który powinien być implementowany przez wszystkie rodzaje żądań do API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp\Request
 */
interface ApiRequestInterface
{
    /**
     * Zwraca w pełni przygotowany adres URL żądania do API NBP
     *
     * @return string
     * @throws IncompleteRequestException
     */
    public function getUrl(): string;
}
