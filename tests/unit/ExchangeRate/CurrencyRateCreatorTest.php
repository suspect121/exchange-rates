<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Tests\Unit\ExchangeRate;

use App\ExchangeRate\CurrencyRateCreator;
use App\Repository\CurrencyRateRepository;
use App\Repository\CurrencyRepository;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class CurrencyRateCreatorTest extends TestCase
{
    public function testCreate(): void
    {
        $creator = $this->getCurrencyRateCreatorAndExpectingNotUseRepository();

        $date = new DateTimeImmutable('2023-04-20');
        $currency_rate = $creator->create('euro', 'EUR', 4.5158, $date);
        $currency = $currency_rate->getCurrency();
        $this->assertSame('euro', $currency->getName());
        $this->assertSame('EUR', $currency->getCode());
        $this->assertSame(4.5158, $currency_rate->getExchangeRate());
        $this->assertSame($date, $currency_rate->getDate());
    }

    public function testCreateTwoExchangeRatesForOneCurrency(): void
    {
        $creator = $this->getCurrencyRateCreatorAndExpectingNotUseRepository();

        $date = new DateTimeImmutable('2023-04-20');
        $currency_rate = $creator->create('euro', 'EUR', 4.51, $date);
        $currency_1 = $currency_rate->getCurrency();

        $date = new DateTimeImmutable('2023-04-21');
        $currency_rate = $creator->create('euro', 'EUR', 4.49, $date);
        $currency_2 = $currency_rate->getCurrency();

        $this->assertSame($currency_1, $currency_2);
    }

    public function testCreateFewExchangeRatesForOneCurrencyAndSave(): void
    {
        $creator = $this->getCurrencyRateCreatorAndExpectingUseRepository(1, 2);

        $creator->create('euro', 'EUR', 4.5140, new DateTimeImmutable('2023-04-20'));
        $creator->create('euro', 'EUR', 4.4985, new DateTimeImmutable('2023-04-21'));

        $creator->save();
    }

    public function testCreateFewExchangeRatesForTwoCurrencyAndSave(): void
    {
        $creator = $this->getCurrencyRateCreatorAndExpectingUseRepository(2, 6);

        $date = new DateTimeImmutable('2023-04-20');
        $creator->create('euro', 'EUR', 4.5178, $date);
        $creator->create('dolar amerykański', 'USD', 4.35, $date);

        $date = new DateTimeImmutable('2023-04-21');
        $creator->create('euro', 'EUR', 4.4931, $date);
        $creator->create('dolar amerykański', 'USD', 4.37, $date);

        $date = new DateTimeImmutable('2023-04-22');
        $creator->create('euro', 'EUR', 4.4751, $date);
        $creator->create('dolar amerykański', 'USD', 4.3858, $date);

        $creator->save();
    }

    /**
     * Zwraca instancję CurrencyRate z załadowanymi imitacjami repozytoriów
     *
     * Zwrócone imitacje repozytoriów są z oczekiwaniem, że metoda "save" nie będzie w nich używana.
     *
     * @return CurrencyRateCreator
     */
    private function getCurrencyRateCreatorAndExpectingNotUseRepository(): CurrencyRateCreator
    {
        /* Imitacja CurrencyRepository */
        $currency_repository = $this->getMockBuilder(CurrencyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency_repository->expects($this->never())
            ->method('save');
        $currency_repository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        /* Imitacja CurrencyRateRepository */
        $currency_rate_repository = $this->getMockBuilder(CurrencyRateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency_rate_repository->expects($this->never())
            ->method('save');

        return new CurrencyRateCreator($currency_repository, $currency_rate_repository);
    }

    /**
     * Zwraca instancję CurrencyRate z załadowanymi imitacjami repozytoriów
     *
     * Zwracane imitacje repozytoriów są z oczekiwaniem, że metoda "save" zostanie w nich wywołana określoną ilość razy.
     *
     * @param int $count_save_currency Spodziewana liczba wywołań metody "save" z CurrencyRepository
     * @param int $count_save_currency_rate Spodziewana liczba wywołań metody "save" z CurrencyRateRepository
     * @return CurrencyRateCreator
     */
    private function getCurrencyRateCreatorAndExpectingUseRepository(
        int $count_save_currency,
        int $count_save_currency_rate
    ): CurrencyRateCreator
    {
        /* Imitacja CurrencyRepository */
        $currency_repository = $this->getMockBuilder(CurrencyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency_repository->expects($this->exactly($count_save_currency))
            ->method('save');
        $currency_repository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        /* Imitacja CurrencyRateRepository */
        $currency_rate_repository = $this->getMockBuilder(CurrencyRateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency_rate_repository->expects($this->exactly($count_save_currency_rate))
            ->method('save');

        return new CurrencyRateCreator($currency_repository, $currency_rate_repository);
    }
}
