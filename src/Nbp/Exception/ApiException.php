<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Exception;

/**
 * Wyjątek reprezentujący ogólny błąd związany z wykorzystaniem API
 *
 * Niniejsza wyjątek powinien być rozszerzany przez wszystkie pozostałe wyjątki związane z wykorzystaniem API NBP.
 * Ma na celu ułatwienie przechwytywania wyjątków związanych z wykorzystaniem API NBP.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp\Exception
 */
class ApiException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
