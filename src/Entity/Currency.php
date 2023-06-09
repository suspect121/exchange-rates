<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Exception;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 3)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $name;

    public function __construct(string $code, string $name)
    {
        if (strlen($code) !== 3) {
            throw new Exception('Nieprawidłowy kod waluty "'.$code.'"');
        }
        $this->id = Uuid::v4();
        $this->code = $code;
        $this->name = $name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
