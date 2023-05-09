<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use App\Nbp\Validator\ExchangeRatesTableValidator;

/**
 * Realizuje komunikację z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class Api
{
    public function __construct(ExchangeRatesTableValidator $exchange_rate_table_validator)
    {

    }

    /**
     * Wykonuje wcześniej przygotowane żądanie
     *
     * @param ApiRequest $api_request
     */
    public function execute(ApiRequest $api_request): void
    {

    }

    /**
     * Zwraca odpowiedź z serwera HTTP
     *
     * @return ApiResponse
     */
    public function getResponse(): ApiResponse
    {

    }
}
