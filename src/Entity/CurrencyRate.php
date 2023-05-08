<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
#[ORM\Index(columns: ['date'], name: 'date_idx')]
class CurrencyRate
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    #[ORM\Column]
    private float $exchange_rate;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $date;

    #[ORM\Column]
    private DateTimeImmutable $created_at;

    public function __construct(Currency $currency, float $exchange_rate, DateTimeImmutable $date)
    {
        $this->id = Uuid::v4();
        $this->currency = $currency;
        $this->exchange_rate = $exchange_rate;
        $this->date = $date;
        $this->created_at = new DateTimeImmutable('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getExchangeRate(): float
    {
        return $this->exchange_rate;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }
}
