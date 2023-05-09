<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\DataSource;

use App\ExchangeRate\CurrencyRateCreator;
use DateTimeImmutable;

/**
 * Umożliwia uzyskanie danych na temat kursów walut z API NBP w formie encji
 *
 * Przed zwróceniem encji, są one zapisywane w bazie danych w celu ich ponownego użycia.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\DataSource
 */
class NbpApiDataSource implements DataSourceInterface
{
    public function __construct(private CurrencyRateCreator $currency_rate_creator)
    {

    }

    /**
     * @inheritDoc
     */
    public function setDate(DateTimeImmutable $date): void
    {

    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {

    }
}
