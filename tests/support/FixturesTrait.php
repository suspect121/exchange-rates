<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

namespace App\Tests\Support;

use App\DataFixtures\BasicFixtures;

trait FixturesTrait
{
    use EntityManagerTrait;

    /**
     * Ładuje przykładowe podstawowe dane do bazy
     */
    private function loadBasicFixtures(): void
    {
        $entity_manager = $this->getEntityManager();
        $basic_fixtures = new BasicFixtures();
        $basic_fixtures->load($entity_manager);
    }
}
