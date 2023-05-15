<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\Exception;

/**
 * Wyjątek reprezentujący ogólny błąd dotyczący uzyskiwania kursów walut
 *
 * Wyjątek ten powinien być rozszerzany przez wszystkie inne wyjątki powiązane z klasami wysokiego poziomu, których
 * celem jest uzyskanie kursów walut.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\Exception
 */
class ExchangeRateException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
