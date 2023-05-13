<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\Nbp\Validator;

use App\Nbp\ApiResponse;
use App\Nbp\Exception\ValidateException;
use App\Nbp\Validator\ExchangeRatesTableValidator;
use App\Tests\Support\NbpExampleDataTrait;
use PHPUnit\Framework\TestCase;

class ExchangeRatesTableValidatorTest extends TestCase
{
    use NbpExampleDataTrait;

    /**
     * Test z prawidłowymi danymi
     */
    public function testValidateCorrectData(): void
    {
        $data = $this->getExampleExchangeRatesTableAsArray();
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test z niedozwolonym kluczem "test" znajdującym się najwyżej w hierarchii danych
     */
    public function testValidateNotAllowedMainKey(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API zawiera niedozwolony klucz "test"');
        $data = $this->getExampleExchangeRatesTableAsArray();
        $data['test'] = 'BAD KEY';
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test z brakiem wymaganego klucza "rates"
     */
    public function testValidateNotContainsMainKey(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API nie zawiera wymaganego klucza "rates"');
        $data = $this->getExampleExchangeRatesTableAsArray();
        unset($data['rates']);
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test z nieprawidłową datą zwróconych danych
     */
    public function testValidateBadDate(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API zawiera nieprawidłowe dane dla klucza "effectiveDate": BAD DATE');
        $data = $this->getExampleExchangeRatesTableAsArray();
        $data['effectiveDate'] = 'BAD DATE';
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test z niedozwolonym kluczem "test" znajdującym się w danych dotyczących waluty
     */
    public function testValidateNotAllowedCurrencyRateKey(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API zawiera niedozwolony klucz "test"');
        $data = $this->getExampleExchangeRatesTableAsArray();
        $data['rates'][5]['test'] = 'BAD KEY';
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test bez wymaganego klucza "mid" który powinien znajdować się w danych dotyczących waluty
     */
    public function testValidateNotContainsCurrencyRateKey(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API nie zawiera wymaganego klucza "mid"');
        $data = $this->getExampleExchangeRatesTableAsArray();
        unset($data['rates'][7]['mid']);
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test z nieprawidłową wartością klucza "mid" który reprezentuje kurs waluty
     */
    public function testValidateBadCurrencyRateMid(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API zawiera nieprawidłowy typ danych dla klucza "mid": string');
        $data = $this->getExampleExchangeRatesTableAsArray();
        $data['rates'][10]['mid'] = 'BAD DATA';
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Test z nieprawidłową wartością klucza "code" który reprezentuje kod waluty
     */
    public function testValidateBadCurrencyRateCode(): void
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessage('Odpowiedź z API zawiera nieprawidłowe dane dla klucza "code": BAD CODE');
        $data = $this->getExampleExchangeRatesTableAsArray();
        $data['rates'][6]['code'] = 'BAD CODE';
        $response = $this->getResponseMock($data);
        $validator = new ExchangeRatesTableValidator();
        $validator->validate($response);
    }

    /**
     * Zwraca symulowaną instancję ApiResponse
     *
     * Niniejsza instancja wymaga co najmniej jednego użycia metody "getResponseArray".
     *
     * @param array $response_data Dane które mają zostać zwrócone przez metodę "getResponseArray"
     * @return ApiResponse
     */
    private function getResponseMock(array $response_data): ApiResponse
    {
        $mock = $this->getMockBuilder(ApiResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())
            ->method('getResponseArray')
            ->willReturn($response_data);
        return $mock;
    }
}
