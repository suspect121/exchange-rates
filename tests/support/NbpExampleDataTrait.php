<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Tests\Support;

trait NbpExampleDataTrait
{
    private static string $exchange_rates_table;
    private static array $exchange_rates_table_array;

    /**
     * Zwraca przykładowe dane wcześniej przygotowane w pliku exchange_rates_table.json
     *
     * Dane zwracane są z zastosowaniem lokalnego mechanizmu cache w celu ograniczenia odczytów pliku.
     *
     * @return string
     */
    private function getExampleExchangeRatesTable(): string
    {
        if (isset(self::$exchange_rates_table)) {
            return self::$exchange_rates_table;
        }
        self::$exchange_rates_table = file_get_contents(__DIR__.'/../data/exchange_rates_table.json');
        return self::$exchange_rates_table;
    }

    /**
     * Zwraca przykładowe dane wcześniej przygotowane w pliku exchange_rates_table.json
     *
     * Niniejsza metoda zwraca przetworzone dane w formie tablicy. Dane zwracane są z zastosowaniem lokalnego mechanizmu
     * cache.
     *
     * @return array
     */
    private function getExampleExchangeRatesTableAsArray(): array
    {
        if (isset(self::$exchange_rates_table_array)) {
            return self::$exchange_rates_table_array;
        }
        $data = $this->getExampleExchangeRatesTable();
        self::$exchange_rates_table_array = json_decode($data, true)[0];
        return self::$exchange_rates_table_array;
    }
}
