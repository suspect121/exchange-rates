<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\Exception;

/**
 * Wyjątek reprezentujący błąd powstały w wyniku braku dzisiejszych danych z wybranego źródła danych
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\Exception
 */
class TodayNoDataException extends NoDataException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
