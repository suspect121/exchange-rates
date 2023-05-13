<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\Exception;

/**
 * Wyjątek reprezentujący ogólny błąd dotyczący źródła danych
 *
 * Wyjątek ten powinien być rozszerzany przez wszystkie inne wyjątki dotyczące źródła danych.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\Exception
 */
class DataSourceException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
