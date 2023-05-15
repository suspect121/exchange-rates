<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\Nbp;

use App\Nbp\Exception\ValidateException;
use App\Nbp\Request\ApiRequestInterface;
use App\Nbp\Validator\ExchangeRatesTableValidator;
use App\Nbp\ValidatorProvider;
use PHPUnit\Framework\TestCase;

class ValidatorProviderTest extends TestCase
{
    /**
     * Test z użyciem żądania dla którego nie istnieje walidator odpowiedzi
     */
    public function testUnknownRequest(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Nie można uzyskać odpowiedniego walidatora odpowiedzi');
        $request = $this->getMockApiRequestWithGetNameMethod('example_request');
        $validator_provider = $this->getValidatorProvider();
        $validator_provider->getValidator($request);
    }

    /**
     * Test z użyciem żądania o nazwie "exchange_rates_table"
     */
    public function testExchangeRatesTableRequest(): void
    {
        $request = $this->getMockApiRequestWithGetNameMethod('exchange_rates_table');
        $validator_provider = $this->getValidatorProvider();
        $validator_provider->getValidator($request);
    }

    /**
     * Zwraca symulowaną instancję implementującą interfejs ApiRequestInterface
     *
     * Niniejsza instancja wymaga co najmniej jednego użycia metody "getName".
     *
     * @param string $request_name Nazwa która ma zostać zwrócona przez metodę "getName"
     * @return ApiRequestInterface
     */
    private function getMockApiRequestWithGetNameMethod(string $request_name): ApiRequestInterface
    {
        $mock = $this->getMockBuilder(ApiRequestInterface::class)
            ->getMock();
        $mock->expects($this->once())
            ->method('getName')
            ->willReturn($request_name);
        return $mock;
    }

    /**
     * Zwraca instancję ValidatorProvider utworzoną z użyciem symulowanych zależności
     *
     * @return ValidatorProvider
     */
    private function getValidatorProvider(): ValidatorProvider
    {
        $exchange_rates_table_validator = $this->getMockBuilder(ExchangeRatesTableValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        return new ValidatorProvider($exchange_rates_table_validator);
    }
}
