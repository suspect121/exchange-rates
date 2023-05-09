<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Validator;

use App\Nbp\ApiResponse;

/**
 * Walidator odpowiedzi z API NBP dotyczącej kursów wymiany walut z wybranej tabeli
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class ExchangeRatesTableValidator implements ValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(ApiResponse $api_response): void
    {

    }
}
