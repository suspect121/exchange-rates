<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Exception;

/**
 * Wyjątek reprezentujący błąd zgłoszony w wyniku przetwarzania odpowiedzi z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp\Exception
 */
class ResponseException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
