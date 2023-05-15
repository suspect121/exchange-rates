<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Tests\Unit\ExchangeRate\DataSource;

use App\Entity\CurrencyRate;
use App\ExchangeRate\DataSource\DatabaseDataSource;
use App\ExchangeRate\Exception\DataSourceException;
use App\Repository\CurrencyRateRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class DatabaseDataSourceTest extends TestCase
{
    public function testGetData(): void
    {
        $date = new DateTimeImmutable('2023-04-12');

        $currency_rate = $this->getMockCurrencyRate();

        $repository = $this->getMockCurrencyRateRepository();
        $repository->expects($this->once())
            ->method('findByDateAndLoadRelation')
            ->with($date)
            ->willReturn([$currency_rate]);

        $data_source = new DatabaseDataSource($repository);
        $data_source->setDate($date);
        $data = $data_source->getData();

        $this->assertCount(1, $data);
        $this->assertSame($currency_rate, $data[0]);
    }

    public function testGetDataWithoutSetDate(): void
    {
        $this->expectException(DataSourceException::class);
        $this->expectExceptionMessage('Nie przekazano daty której mają dotyczyć zwracane kursy walut');

        $repository = $this->getMockCurrencyRateRepository();

        $data_source = new DatabaseDataSource($repository);
        $data_source->getData();
    }

    /**
     * Zwraca imitację CurrencyRateRepository
     *
     * @return CurrencyRateRepository&MockObject
     */
    private function getMockCurrencyRateRepository(): CurrencyRateRepository
    {
        return $this->getMockBuilder(CurrencyRateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Zwraca imitację CurrencyRate
     *
     * @return CurrencyRate&MockObject
     */
    private function getMockCurrencyRate(): CurrencyRate
    {
        return $this->getMockBuilder(CurrencyRate::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
