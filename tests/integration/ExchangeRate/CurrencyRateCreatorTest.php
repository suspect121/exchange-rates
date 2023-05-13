<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Tests\Integration\ExchangeRate;

use App\ExchangeRate\CurrencyRateCreator;
use App\Tests\Support\ClearDatabaseTrait;
use App\Tests\Support\EntityCountTrait;
use App\Tests\Support\EntityManagerTrait;
use App\Tests\Support\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use DateTimeImmutable;

class CurrencyRateCreatorTest extends KernelTestCase
{
    use ClearDatabaseTrait;
    use EntityCountTrait;
    use EntityManagerTrait;
    use FixturesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $this->clearDatabase();
    }

    /**
     * Test tworzenia i zapisu pojedynczej encji oraz weryfikacja jej danych
     */
    public function testCreateAndSave(): void
    {
        $creator = $this->getCurrencyRateCreator();

        /* Tworzenie encji wraz z relacją i zapis do bazy danych */
        $date = new DateTimeImmutable('2023-04-14');
        $currency_rate = $creator->create('euro', 'EUR', 4.3853, $date);
        $creator->save();

        /* Weryfikacja danych z utworzonej encji */
        $currency = $currency_rate->getCurrency();
        $this->assertSame('euro', $currency->getName());
        $this->assertSame('EUR', $currency->getCode());
        $this->assertSame(4.3853, $currency_rate->getExchangeRate());
        $this->assertSame($date, $currency_rate->getDate());

        /* Weryfikacja ilości rekordów w tabelach */
        $this->assertSame(1, $this->getCurrencyCount());
        $this->assertSame(1, $this->getCurrencyRateCount());
    }

    /**
     * Test tworzenia i zapisu encji z kursami walut dla dwóch walut
     */
    public function testCreateAndSaveFourExchangeRatesForTwoCurrency(): void
    {
        $creator = $this->getCurrencyRateCreator();

        /* Tworzenie encji wraz z relacją i zapis do bazy danych */
        $date = new DateTimeImmutable('2023-04-11');
        $creator->create('dolar amerykański', 'USD', 4.32, $date);
        $creator->create('euro', 'EUR', 4.5731, $date);
        $date = new DateTimeImmutable('2023-04-12');
        $creator->create('dolar amerykański', 'USD', 4.35, $date);
        $creator->create('euro', 'EUR', 4.5475, $date);
        $creator->save();

        /* Weryfikacja ilości rekordów w tabelach */
        $this->assertSame(2, $this->getCurrencyCount());
        $this->assertSame(4, $this->getCurrencyRateCount());
    }

    /**
     * Test tworzenia i zapisu wielu różnych kursów wymiany walut
     */
    public function testCreateAndSaveManyExchangeRates(): void
    {
        $creator = $this->getCurrencyRateCreator();

        /* Tworzenie encji wraz z relacją i zapis do bazy danych */
        $date = new DateTimeImmutable('2023-04-11');
        $creator->create('dolar amerykański', 'USD', 4.3267, $date);
        $creator->create('dolar australijski', 'AUD', 2.7853, $date);
        $creator->create('euro', 'EUR', 4.5726, $date);
        $creator->create('frank szwajcarski', 'CHF', 4.67, $date);

        $date = new DateTimeImmutable('2023-04-12');
        $creator->create('dolar amerykański', 'USD', 4.3037, $date);
        $creator->create('dolar australijski', 'AUD', 2.8010, $date);
        $creator->create('euro', 'EUR', 4.5461, $date);
        $creator->create('frank szwajcarski', 'CHF', 4.6655, $date);

        $date = new DateTimeImmutable('2023-04-13');
        $creator->create('dolar amerykański', 'USD', 4.3163, $date);
        $creator->create('dolar australijski', 'AUD', 2.7627, $date);
        $creator->create('euro', 'EUR', 4.5597, $date);
        $creator->create('frank szwajcarski', 'CHF', 4.6464, $date);

        $creator->save();

        /* Weryfikacja ilości rekordów w tabelach */
        $this->assertSame(4, $this->getCurrencyCount());
        $this->assertSame(12, $this->getCurrencyRateCount());
    }

    /**
     * Test tworzenia i zapisu wielu różnych kursów wymiany walut z już istniejącymi danymi w bazie
     */
    public function testCreateAndSaveManyExchangeRatesForExistCurrencies(): void
    {
        /* Ładowanie do bazy przykładowych danych i weryfikacja ich ilości */
        $this->loadBasicFixtures();
        $this->assertSame(4, $this->getCurrencyCount());
        $this->assertSame(8, $this->getCurrencyRateCount());

        /* Uzyskiwanie instancji CurrencyRateCreator */
        $creator = $this->getCurrencyRateCreator();

        /* Tworzenie encji wraz z relacją i zapis do bazy danych */
        $date = new DateTimeImmutable('2023-04-12');
        $creator->create('dolar amerykański', 'USD', 4.3350, $date);
        $creator->create('dolar australijski', 'AUD', 2.8025, $date);
        $creator->create('euro', 'EUR', 4.5447, $date);
        $creator->create('frank szwajcarski', 'CHF', 4.66, $date);
        $creator->create('jen (Japonia)', 'JPY', 3.0814, $date);
        $creator->create('lira turecka', 'TRY', 0.2123, $date);

        $creator->save();

        /* Weryfikacja ilości rekordów w tabelach */
        $this->assertSame(6, $this->getCurrencyCount());
        $this->assertSame(14, $this->getCurrencyRateCount());
    }

    /**
     * Zwraca instancję CurrencyRateCreator z kontenera zależności
     *
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    private function getCurrencyRateCreator(): CurrencyRateCreator
    {
        return self::getContainer()
            ->get(CurrencyRateCreator::class);
    }
}
