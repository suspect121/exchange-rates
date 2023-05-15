<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\ExchangeRate\DataSource;

use App\ExchangeRate\Exception\DataSourceException;
use App\Repository\CurrencyRateRepository;
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
    private DateTimeImmutable $date;

    public function __construct(private CurrencyRateRepository $currency_rate_repository)
    {

    }

    /**
     * @inheritDoc
     */
    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $this->checkSetDate();
        return $this->currency_rate_repository
            ->findByDateAndLoadRelation($this->date);
    }

    /**
     * Sprawdza czy ustawiono datę której mają dotyczyć zwracane kursy walut
     *
     * @throws DataSourceException
     */
    private function checkSetDate(): void
    {
        if (!isset($this->date)) {
            throw new DataSourceException('Nie przekazano daty której mają dotyczyć zwracane kursy walut');
        }
    }
}
