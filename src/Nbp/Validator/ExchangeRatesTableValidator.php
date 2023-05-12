<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Validator;

use App\Nbp\ApiResponse;
use App\Nbp\Exception\ValidateException;

/**
 * Walidator odpowiedzi z API NBP dotyczącej kursów wymiany walut z wybranej tabeli
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp
 */
class ExchangeRatesTableValidator extends Validator implements ValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(ApiResponse $api_response): void
    {
        $response = $api_response->getResponseArray();
        $this->checkMainData($response);
        $this->checkRatesData($response);
    }

    /**
     * Sprawdza podstawowe dane zwrócone przez API NBP
     *
     * Niniejsza metoda sprawdzana klucze będące najwyżej w hierarchii wraz z ich danymi.
     *
     * @param array $response
     * @throws ValidateException
     */
    private function checkMainData(array $response): void
    {
        $allowed_keys = ['table', 'no', 'effectiveDate', 'rates'];
        $required_keys = ['table', 'effectiveDate', 'rates'];
        $expected_properties = [
            'table' => ['type' => 'string', 'pattern' => '/^[A-C]$/'],
            'no' => ['type' => 'string', 'pattern' => '/^[0-9A-Z\/]+\/NBP\/2[0-9]{3}$/'],
            'effectiveDate' => ['type' => 'string', 'pattern' => '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/'],
            'rates' => ['type' => 'array']
        ];
        $this->checkKeys($response, $allowed_keys, $required_keys);
        $this->checkDataContent($response, $expected_properties);
    }

    /**
     * Sprawdza dane zwrócone przez API NBP pod kluczem "rates"
     *
     * @param array $response
     * @throws ValidateException
     */
    private function checkRatesData(array $response): void
    {
        foreach($response['rates'] as $data) {
            $allowed_keys = ['currency', 'code', 'mid'];
            $required_keys = $allowed_keys;
            $expected_properties = [
                'currency' => ['type' => 'string', 'pattern' => '/^[a-zA-Z\p{L}() ]+$/u'],
                'code' => ['type' => 'string', 'pattern' => '/^[A-Z]{3}$/'],
                'mid' => ['type' => 'double']
            ];
            $this->checkKeys($data, $allowed_keys, $required_keys);
            $this->checkDataContent($data, $expected_properties);
        }
    }
}
