<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Validator;

use App\Nbp\Exception\ValidateException;

/**
 * Klasa abstrakcyjna dostarczająca metody pomocnicze dla walidatorów
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp\Validator
 */
abstract class Validator
{
    /**
     * Sprawdza klucze danych pod względem zgodności z oczekiwaniami
     *
     * Sprawdzaniu podlega samo istnienie kluczy danych. Nie są weryfikowane ich wartości.
     *
     * @param array $data Dane których klucze będą sprawdzane
     * @param array $allowed_keys Dozwolone klucze danych
     * @param array $required_keys Wymagane klucze danych
     * @throws ValidateException
     */
    protected function checkKeys(array $data, array $allowed_keys, array $required_keys = []): void
    {
        $data_keys = array_keys($data);
        $this->checkDataHasAllowedKeys($data_keys, $allowed_keys);
        if ($required_keys !== []) {
            $this->checkDataHasRequiredKeys($data_keys, $required_keys);
        }
    }

    /**
     * Sprawdza wszystkie istniejące klucze danych pod względem tego czy są oczekiwane (niekoniecznie wymagane)
     *
     * @param array $data_keys Dostarczone klucze danych
     * @param array $allowed_keys Dozwolone klucze danych
     * @throws ValidateException Wyjątek zwracany, gdy któryś z dostarczonych kluczy nie jest uznawany, jako oczekiwany
     */
    private function checkDataHasAllowedKeys(array $data_keys, array $allowed_keys): void
    {
        $diff_allowed_keys = array_diff($data_keys, $allowed_keys);
        if ($diff_allowed_keys !== []) {
            if (count($diff_allowed_keys) > 1) {
                $message = 'Odpowiedź z API zawiera niedozwolone klucze: '.implode(' ', $diff_allowed_keys);
            } else {
                $message = 'Odpowiedź z API zawiera niedozwolony klucz "'.current($diff_allowed_keys).'"';
            }
            throw new ValidateException($message);
        }
    }

    /**
     * Sprawdza czy zostały dostarczone wszystkie wymagane klucze danych
     *
     * @param array $data_keys Dostarczone klucze danych
     * @param array $required_keys Wymagane klucze danych
     * @throws ValidateException Wyjątek zwracany, gdy nie wszystkie wymagane klucze zostały dostarczone
     */
    private function checkDataHasRequiredKeys(array $data_keys, array $required_keys): void
    {
        $diff_required_keys = array_diff($required_keys, $data_keys);
        if ($diff_required_keys !== []) {
            if (count($diff_required_keys) > 1) {
                $message = 'Odpowiedź z API nie zawiera wymaganych kluczy: '.implode(' ', $diff_required_keys);
            } else {
                $message = 'Odpowiedź z API nie zawiera wymaganego klucza "'.current($diff_required_keys).'"';
            }
            throw new ValidateException($message);
        }
    }

    /**
     * Sprawdza dane pod względem ich typów i treści
     *
     * @param array $data Dane które mają być przedmiotem weryfikacji
     * @param array $expected_properties Spodziewane właściwości danych. Powinny być one przekazane w formie tablicy
     *                                   gdzie każdy z kluczy tablicy, odpowiada nazwą kluczowi znajdującemu się w
     *                                   weryfikowanych danych. W ten sposób następuje powiązanie oczekiwanych
     *                                   właściwości danych z danymi, które mają być przedmiotem weryfikacji.
     *
     *                                   Dozwolone spodziewane właściwości danych to:
     *                                   <div><b>type</b> - Typ danych, np. integer czy string (pełne nazwy)</div>
     *                                   <div><b>pattern</b> - Wzorzec danych (dotyczy jedynie typu string)
     * @throws ValidateException
     */
    protected function checkDataContent(array $data, array $expected_properties): void
    {
        foreach ($data as $key => $value) {
            $expected = $expected_properties[$key];
            $value_type = gettype($value);
            if ($expected['type'] !== $value_type) {
                $message = 'Odpowiedź z API zawiera nieprawidłowy typ danych dla klucza "'.$key.'": '.$value_type;
                throw new ValidateException($message);
            }
            if (isset($expected['pattern']) && $value_type === 'string' && !preg_match($expected['pattern'], $value)) {
                $message = 'Odpowiedź z API zawiera nieprawidłowe dane dla klucza "'.$key.'": '.$value;
                throw new ValidateException($message);
            }
        }
    }
}
