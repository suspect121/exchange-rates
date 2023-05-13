<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use App\Nbp\Exception\ResponseException;

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
    public function __construct(private int $status, private string $response_body)
    {

    }

    /**
     * Sprawdza czy odpowiedź serwera HTTP to status 200
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status === 200;
    }

    /**
     * Zwraca status zwrócony przez serwer HTTP
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Zwraca odpowiedź z serwera HTTP przetworzoną do formy tablicy
     *
     * @return array
     * @throws ResponseException
     */
    public function getResponseArray(): array
    {
        if ($this->status !== 200) {
            $message = 'Nie można uzyskać treści odpowiedzi ponieważ serwer HTTP zwrócił kod błędu '.$this->status;
            throw new ResponseException($message);
        }
        $response_array = json_decode($this->response_body, true);
        if (!is_array($response_array)) {
            throw new ResponseException('Przetworzenie odpowiedzi z API NBP nie powiodło się');
        }
        /* Zwracanie danych niższego poziomu, jeżeli jedyny istniejący klucz najwyższego poziomu to "0" */
        if (count($response_array) === 1 && isset($response_array[0])) {
            return $response_array[0];
        }
        return $response_array;
    }
}
