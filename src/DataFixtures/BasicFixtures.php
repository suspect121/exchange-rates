<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;

class BasicFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            '2023-04-17' => [
                [
                    'code' => 'USD',
                    'name' => 'dolar amerykański',
                    'exchange_rate' => 4.2261
                ],
                [
                    'code' => 'AUD',
                    'name' => 'dolar australijski',
                    'exchange_rate' => 2.8309
                ],
                [
                    'code' => 'EUR',
                    'name' => 'euro',
                    'exchange_rate' => 4.6341
                ],
                [
                    'code' => 'CHF',
                    'name' => 'frank szwajcarski',
                    'exchange_rate' => 4.7222
                ]
            ],
            '2023-04-18' => [
                [
                    'code' => 'USD',
                    'name' => 'dolar amerykański',
                    'exchange_rate' => 4.2151
                ],
                [
                    'code' => 'AUD',
                    'name' => 'dolar australijski',
                    'exchange_rate' => 2.8397
                ],
                [
                    'code' => 'EUR',
                    'name' => 'euro',
                    'exchange_rate' => 4.6286
                ],
                [
                    'code' => 'CHF',
                    'name' => 'frank szwajcarski',
                    'exchange_rate' => 4.7031
                ]
            ]
        ];

        /* Tworzenie encji Currency */
        $currencies = [];
        $first_date = array_key_first($data);
        foreach($data[$first_date] as $value)
        {
            $currency = new Currency($value['code'], $value['name']);
            $currencies[$value['code']] = $currency;
            $manager->persist($currency);
        }

        /* Tworzenie encji CurrencyRate */
        foreach($data as $date => $child_data)
        {
            foreach($child_data as $value)
            {
                $currency = $currencies[$value['code']];
                $currency_rate = new CurrencyRate($currency, $value['exchange_rate'], new DateTimeImmutable($date));
                $manager->persist($currency_rate);
            }
        }

        $manager->flush();
    }
}
