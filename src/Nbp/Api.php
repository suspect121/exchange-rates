<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

/**
 * Realizuje komunikację z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class Api
{
    public function __construct(ApiResponseValidate $api_response_validate)
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
