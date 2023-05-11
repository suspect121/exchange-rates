<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Request;

use App\Nbp\Exception\BadArgumentException;
use App\Nbp\Exception\IncompleteRequestException;
use DateTimeImmutable;

/**
 * Reprezentuje żądanie do API NBP które ma na celu uzyskanie kursów walut z wybranej tabeli i daty
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class ExchangeRatesTableRequest implements ApiRequestInterface
{
    private DateTimeImmutable $date;
    private string $table;

    /**
     * Ustawia tabelę kursów walut której ma dotyczyć żądanie
     *
     * Dostępne tabele to A, B, C.
     *
     * @param string $table
     * @throws BadArgumentException
     */
    public function setTable(string $table): void
    {
        if(!preg_match('/^[a-cA-C]$/', $table))
        {
            throw new BadArgumentException('Nieprawidłowa tabela kursów walut');
        }
        $this->table = strtolower($table);
    }

    /**
     * Ustawia datę kursów walut której ma dotyczyć żądanie
     *
     * @param DateTimeImmutable $date
     */
    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'exchange_rates_table';
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $this->checkSetTable();
        $this->checkSetDate();
        $date_param = $this->date
            ->format('Y-m-d');
        $today_date = date('Y-m-d');
        if($date_param === $today_date)
        {
            $date_param = 'today';
        }
        return 'https://api.nbp.pl/api/exchangerates/tables/'.$this->table.'/'.$date_param.'/';
    }

    /**
     * @throws IncompleteRequestException
     */
    private function checkSetTable()
    {
        if(!isset($this->table))
        {
            throw new IncompleteRequestException('Nie przekazano tabeli której mają dotyczyć zwracane kursy walut');
        }
    }

    /**
     * @throws IncompleteRequestException
     */
    private function checkSetDate()
    {
        if(!isset($this->date))
        {
            throw new IncompleteRequestException('Nie przekazano daty której mają dotyczyć zwracane kursy walut');
        }
    }
}
