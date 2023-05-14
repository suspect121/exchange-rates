<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\ExchangeRate;

use App\Entity\CurrencyRate;
use App\ExchangeRate\DataSource\DatabaseDataSource;
use App\ExchangeRate\DataSource\DataSourceInterface;
use App\ExchangeRate\DataSource\NbpApiDataSource;
use App\ExchangeRate\Exception\ExchangeRateException;
use App\ExchangeRate\Exception\NoRequiredParametersException;
use DateTimeImmutable;

/**
 * Wybiera odpowiednie źródło danych i dostarcza z niego encje CurrencyRate
 *
 * Wybrane źródło danych zależy od tego, czy dane które mają zostać uzyskane, istnieją już w bazie danych.
 * Wyższy priorytet jako źródło danych ma baza danych. W przypadku braku możliwości uzyskania danych z bazy, następuje
 * wybór źródła danych o niższym priorytecie, czyli API NBP.
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\ExchangeRate
 */
class CurrencyRateProvider
{
    /** @var DataSourceInterface[] $data_sources */
    private array $data_sources = [];
    private DateTimeImmutable $date;

    public function __construct(DatabaseDataSource $database_data_source, NbpApiDataSource $nbp_api_data_source)
    {
        $this->data_sources[] = $database_data_source;
        $this->data_sources[] = $nbp_api_data_source;
    }

    /**
     * Ustawia datę publikacji kursów walut której mają dotyczyć zwracane encje
     *
     * @param DateTimeImmutable $date
     */
    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * Zwraca encje CurrencyRate pasujące do wcześniej zdefiniowanych parametrów
     *
     * @return CurrencyRate[] array
     * @throws ExchangeRateException
     */
    public function getData(): array
    {
        $this->checkSetDate();
        $data = [];
        foreach ($this->data_sources as $data_source) {
            $data_source->setDate($this->date);
            $data = $data_source->getData();
            if ($data !== []) {
                break;
            }
        }
        return $data;
    }

    /**
     * Sprawdza czy ustawiono datę której mają dotyczyć zwracane kursy walut
     *
     * @throws NoRequiredParametersException
     */
    private function checkSetDate(): void
    {
        if (!isset($this->date)) {
            throw new NoRequiredParametersException('Nie przekazano daty której mają dotyczyć zwracane kursy walut');
        }
    }
}
