<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use App\Nbp\Validator\ExchangeRatesTableValidator;
use Exception;

/**
 * Weryfikuje czy dane zwrócone przez API NBP są zgodne z oczekiwaniami
 *
 * Niniejsza klasa ma na celu podniesienie bezpieczeństwa działania aplikacji przez weryfikację danych zwróconych przez
 * API NBP. Weryfikacja danych zwróconych przez API jest dobrą praktyką.
 *
 * Weryfikacja oferowana przez niniejszą klasę polega na sprawdzeniu wszystkich kluczy i typów otrzymanych
 * danych.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class ApiResponseValidate
{
    private array $validators = [];

    public function __construct(ExchangeRatesTableValidator $exchange_rates_table_validator)
    {
        $this->validators['ExchangeRatesTable'] = $exchange_rates_table_validator;
    }

    /**
     * Rozpoczyna weryfikację danych zawartych w przekazanej odpowiedzi z API NBP
     *
     * @param ApiResponse $api_response
     * @throws Exception
     */
    public function validate(ApiResponse $api_response): void
    {

    }
}
