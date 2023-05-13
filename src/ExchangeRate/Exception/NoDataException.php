<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\Exception;

/**
 * Wyjątek reprezentujący błąd powstały w wyniku braku dostępnych danych z wybranego źródła danych
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\Exception
 */
class NoDataException extends DataSourceException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
