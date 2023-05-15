<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\Exception;

/**
 * Wyjątek reprezentujący błąd powstały w sytuacji braku definicji wszystkich wymaganych parametrów w instancji
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\Exception
 */
class NoRequiredParametersException extends ExchangeRateException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
