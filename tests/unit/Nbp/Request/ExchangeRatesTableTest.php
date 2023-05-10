<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\Nbp\Request;

use App\Nbp\Exception\BadArgumentException;
use App\Nbp\Exception\IncompleteRequestException;
use App\Nbp\Request\ExchangeRatesTableRequest;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class ExchangeRatesTableTest extends TestCase
{
    public function testSetBadTable(): void
    {
        $this->expectException(BadArgumentException::class);
        $this->expectExceptionMessage('Nieprawidłowa tabela kursów walut');
        $request = new ExchangeRatesTableRequest();
        $request->setTable('D');
    }

    public function testGetUrlWithoutSetTable(): void
    {
        $this->expectException(IncompleteRequestException::class);
        $this->expectExceptionMessage('Nie przekazano tabeli której mają dotyczyć zwracane kursy walut');
        $request = new ExchangeRatesTableRequest();
        $date = $this->getExampleDate();
        $request->setDate($date);
        $request->getUrl();
    }

    public function testGetUrlWithoutSetDate(): void
    {
        $this->expectException(IncompleteRequestException::class);
        $this->expectExceptionMessage('Nie przekazano daty której mają dotyczyć zwracane kursy walut');
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $request->getUrl();
    }

    public function testCreateExampleRequestAndGetUrl(): void
    {
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $date = $this->getExampleDate();
        $request->setDate($date);
        $request->getUrl();
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/2023-04-18/';
        $this->assertSame($expected_url, $request->getUrl());
    }

    public function testCreateRequestWithTodayDate(): void
    {
        $request = new ExchangeRatesTableRequest();
        $request->setTable('A');
        $date = new DateTimeImmutable(date('Y-m-d'));
        $request->setDate($date);
        $request->getUrl();
        $expected_url = 'https://api.nbp.pl/api/exchangerates/tables/a/today/';
        $this->assertSame($expected_url, $request->getUrl());
    }

    private function getExampleDate(): DateTimeImmutable
    {
        return new DateTimeImmutable('2023-04-18');
    }
}
