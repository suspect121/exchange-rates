<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\ExchangeRate;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\ExchangeRate\CurrencyRateProvider;
use App\ExchangeRate\ExchangeRate;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class ExchangeRateTest extends TestCase
{
    /**
     * Test użycia metody "getExchangeRates" z przekazaniem daty
     */
    public function testGetExchangeRates(): void
    {
        /* Przygotowanie instancji providera wraz z oczekiwaną datą */
        $date = new DateTimeImmutable('2023-04-12');
        $provider = $this->getMockCurrencyRateProvider($date);

        /* Tworzenie instancji ExchangeRate i uzyskiwanie z niej danych */
        $exchange_rate = new ExchangeRate($provider);
        $data = $exchange_rate->getExchangeRates($date);

        /* Weryfikacja rezultatu */
        $this->assertCount(3, $data);
        $expected_data = $this->getExpectedData();
        $this->assertSame($expected_data, $data);
    }

    /**
     * Test użycia metody "getExchangeRates" bez przekazania daty
     *
     * Pominięcie jeżeli poprzedni test nie został zaliczony
     * @depends testGetExchangeRates
     */
    public function testGetExchangeRatesWithoutDate(): void
    {
        /* Przygotowanie instancji providera wraz z oczekiwaną datą (aktualna data bez czasu) */
        $date = new DateTimeImmutable(date('Y-m-d'));
        $provider = $this->getMockCurrencyRateProvider($date);

        /* Tworzenie instancji ExchangeRate i uzyskiwanie z niej danych */
        $exchange_rate = new ExchangeRate($provider);
        $data = $exchange_rate->getExchangeRates();

        /* Weryfikacja rezultatu */
        $this->assertCount(3, $data);
        $expected_data = $this->getExpectedData();
        $this->assertSame($expected_data, $data);
    }

    /**
     * Zwraca spodziewane dane które powinny zostać zwrócone przez metodę "getExchangeRates"
     *
     * @return array[]
     */
    private function getExpectedData(): array
    {
        return [
            [
                'currency_name' => 'dolar amerykański',
                'currency_code' => 'USD',
                'exchange_rate' => 4.4152,
            ],
            [
                'currency_name' => 'euro',
                'currency_code' => 'EUR',
                'exchange_rate' => 4.5757,
            ],
            [
                'currency_name' => 'frank szwajcarski',
                'currency_code' => 'CHF',
                'exchange_rate' => 4.6531,
            ]
        ];
    }

    /**
     * Zwraca imitację CurrencyRateProvider z przykładowymi encjami oraz oczekiwaniem wywołania ich metod
     *
     * @param DateTimeImmutable $expected_date Spodziewana data która powinna być użyta w metodzie "setDate"
     * @return CurrencyRateProvider
     */
    private function getMockCurrencyRateProvider(DateTimeImmutable $expected_date): CurrencyRateProvider
    {
        $currency_rates = [
            $this->getMockCurrencyRate('dolar amerykański', 'USD', 4.4152),
            $this->getMockCurrencyRate('euro', 'EUR', 4.5757),
            $this->getMockCurrencyRate('frank szwajcarski', 'CHF', 4.6531)
        ];
        $provider = $this->getMockBuilder(CurrencyRateProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $provider->expects($this->once())
            ->method('setDate')
            ->with($expected_date);
        $provider->expects($this->once())
            ->method('getData')
            ->willReturn($currency_rates);
        return $provider;
    }

    /**
     * Zwraca imitację encji CurrencyRate wraz z jej relacją czyli Currency
     *
     * @param string $name Nazwa waluty
     * @param string $code Kod waluty
     * @param float $exchange_rate Kurs wymiany
     * @return CurrencyRate
     */
    private function getMockCurrencyRate(string $name, string $code, float $exchange_rate): CurrencyRate
    {
        $currency = $this->getMockCurrency($name, $code);
        $currency_rate = $this->getMockBuilder(CurrencyRate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency_rate->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currency);
        $currency_rate->expects($this->once())
            ->method('getExchangeRate')
            ->willReturn($exchange_rate);
        return $currency_rate;
    }

    /**
     * Zwraca imitację encji Currency
     *
     * @param string $name
     * @param string $code
     * @return Currency
     */
    private function getMockCurrency(string $name, string $code): Currency
    {
        $currency = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency->expects($this->once())
            ->method('getName')
            ->willReturn($name);
        $currency->expects($this->once())
            ->method('getCode')
            ->willReturn($code);
        return $currency;
    }
}
