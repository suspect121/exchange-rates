<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Tests\Support;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait MockHttpClientTrait
{
    /**
     * Przygotowuje imitację HttpClient a następnie ładuje ją do kontenera zależności
     *
     * @param string $expected_url Spodziewany adres URL żądania
     * @param int $http_code Kod HTTP który ma zostać zwrócony w odpowiedzi
     * @param string $body Treść która ma zostać zwrócona w odpowiedzi
     */
    private function createMockHttpClientAndLoadToContainer(string $expected_url, int $http_code, string $body): void
    {
        $expected_requests = [
            function ($method, $url) use ($expected_url, $http_code, $body) {
                $this->assertSame('GET', $method);
                $this->assertSame($expected_url, $url);
                return new MockResponse($body, ['http_code' => $http_code]);
            }
        ];
        $http_client = new MockHttpClient($expected_requests);
        static::getContainer()
            ->set(HttpClientInterface::class, $http_client);
    }
}
