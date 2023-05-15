<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Tests\Support;

use Doctrine\ORM\EntityManager;

trait EntityManagerTrait
{
    /**
     * Zwraca EntityManager
     *
     * @return EntityManager
     */
    private function getEntityManager(): EntityManager
    {
        return static::getContainer()
            ->get('doctrine')
            ->getManager();
    }
}
