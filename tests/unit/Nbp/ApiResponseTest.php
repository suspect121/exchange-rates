<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\Nbp;

use App\Nbp\ApiResponse;
use App\Nbp\Exception\ResponseException;
use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{
    public function testWithCorrectResponse(): void
    {
        $data = $this->getExampleData();
        $response = new ApiResponse(200, $data);
        $this->assertTrue($response->isSuccess());
        $this->assertSame(200, $response->getStatus());
        $response_array = $response->getResponseArray();
        $this->assertArrayHasKey('rates', $response_array);
        $this->assertCount(33, $response_array['rates']);
    }

    public function testWithHttpError(): void
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Nie można uzyskać treści odpowiedzi ponieważ serwer HTTP zwrócił kod błędu 404');
        $response = new ApiResponse(404, 'test');
        $this->assertFalse($response->isSuccess());
        $this->assertSame(404, $response->getStatus());
        $response->getResponseArray();
    }

    public function testWithBadResponse(): void
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Przetworzenie odpowiedzi z API NBP nie powiodło się');
        $response = new ApiResponse(200, 'test');
        $this->assertTrue($response->isSuccess());
        $this->assertSame(200, $response->getStatus());
        $response->getResponseArray();
    }

    /**
     * Zwraca przykładowe dane wcześniej przygotowane w pliku exchange_rates_table.json
     *
     * @return string
     */
    private function getExampleData(): string
    {
        return file_get_contents(__DIR__.'/../../data/exchange_rates_table.json');
    }
}
