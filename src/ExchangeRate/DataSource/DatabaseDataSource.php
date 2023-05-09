<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate\DataSource;

use DateTimeImmutable;

/**
 * Umożliwia uzyskanie danych na temat kursów walut z bazy danych w formie encji
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate\DataSource
 */
class DatabaseDataSource implements DataSourceInterface
{
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
