<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

/**
 * Reprezentuje odpowiedź z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class ApiResponse
{
    /**
     * @param int $status Status zwrócony przez serwer HTTP
     * @param string $response_body Treść odpowiedzi z serwera HTTP
     */
    public function __construct(int $status, string $response_body)
    {

    }

    /**
     * Sprawdza czy odpowiedź serwera HTTP to status 200
     *
     * @return bool
     */
    public function isSuccess(): bool
    {

    }

    /**
     * Zwraca status zwrócony przez serwer HTTP
     *
     * @return int
     */
    public function getStatus(): int
    {

    }

    /**
     * Zwraca odpowiedź z serwera HTTP przetworzoną do formy tablicy
     *
     * @return array
     */
    public function getResponseArray(): array
    {

    }
}
