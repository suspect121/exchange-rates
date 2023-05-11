<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use App\Nbp\Request\ApiRequestInterface;

/**
 * Realizuje komunikację z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class Api
{
    public function __construct(ValidatorProvider $validator_provider)
    {

    }

    /**
     * Wykonuje wcześniej przygotowane żądanie
     *
     * @param ApiRequestInterface $api_request
     */
    public function execute(ApiRequestInterface $api_request): void
    {

    }

    /**
     * Zwraca odpowiedź z serwera HTTP
     *
     * Przed zwróceniem odpowiedzi, następuje jej walidacja.
     *
     * @return ApiResponse
     */
    public function getResponse(): ApiResponse
    {

    }
}
