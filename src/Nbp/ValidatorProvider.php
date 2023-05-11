<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Nbp;

use App\Nbp\Exception\ValidateException;
use App\Nbp\Request\ApiRequestInterface;
use App\Nbp\Validator\ExchangeRatesTableValidator;
use App\Nbp\Validator\Validator;
use App\Nbp\Validator\ValidatorInterface;

class ValidatorProvider
{
    /**
     * @var Validator[] array
     */
    private array $validators = [];

    public function __construct(ExchangeRatesTableValidator $exchange_rates_table_validator)
    {
        $this->validators['exchange_rates_table'] = $exchange_rates_table_validator;
    }

    /**
     * Zwraca odpowiedni walidator odpowiedzi dla żądania podanego w argumencie
     *
     * @param ApiRequestInterface $api_request Żądanie dla którego odpowiedź ma być weryfikowana
     * @return ValidatorInterface
     * @throws ValidateException
     */
    public function getValidator(ApiRequestInterface $api_request): ValidatorInterface
    {
        $request_name = $api_request->getName();
        if(!isset($this->validators[$request_name]))
        {
            throw new ValidateException('Nie można uzyskać odpowiedniego walidatora odpowiedzi');
        }
        return $this->validators[$request_name];
    }
}
