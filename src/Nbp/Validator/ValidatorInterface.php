<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp\Validator;

use App\Nbp\ApiResponse;
use App\Nbp\Exception\ValidateException;

/**
 * Interfejs który powinien być implementowany przez wszystkie walidatory odpowiedzi z API NBP
 *
 * @author Mateusz Paluszek <bok@servhost.pl>
 * @copyright 2023 Mateusz Paluszek
 * @package App\Nbp\Validator
 */
interface ValidatorInterface
{
    /**
     * Rozpoczyna walidację danych zwróconych przez API NBP
     *
     * W przypadku napotkanego błędu zwracany jest odpowiedni wyjątek.
     *
     * @param ApiResponse $api_response
     * @throws ValidateException
     */
    public function validate(ApiResponse $api_response): void;
}
