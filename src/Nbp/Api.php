<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use App\Nbp\Exception\ApiException;
use App\Nbp\Exception\IncompleteRequestException;
use App\Nbp\Exception\ValidateException;
use App\Nbp\Request\ApiRequestInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Realizuje komunikację z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class Api
{
    private ResponseInterface $response;
    private ApiRequestInterface $api_request;

    public function __construct(private HttpClientInterface $http_client, private ValidatorProvider $validator_provider)
    {

    }

    /**
     * Wykonuje wcześniej przygotowane żądanie
     *
     * @param ApiRequestInterface $api_request
     * @throws ApiException
     * @throws IncompleteRequestException
     */
    public function execute(ApiRequestInterface $api_request): void
    {
        $this->api_request = $api_request;
        $url = $api_request->getUrl();
        try {
            $this->response = $this->http_client
                ->request('GET', $url);
        } catch (TransportExceptionInterface $exception) {
            throw new ApiException('Wystąpił błąd podczas wykonywania żądania do API NBP - '.$exception->getMessage());
        }
    }

    /**
     * Zwraca odpowiedź z serwera HTTP
     *
     * Przed zwróceniem odpowiedzi, następuje jej walidacja.
     *
     * @return ApiResponse
     * @throws ApiException
     * @throws ValidateException
     */
    public function getResponse(): ApiResponse
    {
        $response_data = $this->getResponseData();
        $api_response = new ApiResponse($response_data['status'], $response_data['body']);
        $this->validateApiResponse($response_data['status'], $api_response);
        return $api_response;
    }

    /**
     * Uzyskiwanie danych z odpowiedzi wygenerowanej przez HTTP Client i obsługa wyjątków
     *
     * @return array Podstawowe dane dotyczące odpowiedzi (klucz "status" i "body")
     * @throws ApiException
     */
    private function getResponseData(): array
    {
        $body = '';
        try {
            $status = $this->response
                ->getStatusCode();
            if ($status === 200) {
                $body = $this->response
                    ->getContent();
            }
        } catch (HttpExceptionInterface|TransportExceptionInterface $exception) {
            throw new ApiException('Wystąpił błąd podczas uzyskiwania odpowiedzi z API NBP - '.$exception->getMessage());
        }
        return ['status' => $status, 'body' => $body];
    }

    /**
     * Walidacja treści odpowiedzi uzyskanej z API NBP
     *
     * @param int $http_status Status zwrócony przez serwer HTTP
     * @param ApiResponse $api_response Reprezentacja odpowiedzi uzyskanej z API NBP w postaci instancji ApiResponse
     * @throws ValidateException
     */
    private function validateApiResponse(int $http_status, ApiResponse $api_response): void
    {
        if ($http_status === 200) {
            $validator = $this->validator_provider
                ->getValidator($this->api_request);
            $validator->validate($api_response);
        }
    }
}
