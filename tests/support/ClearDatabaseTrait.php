<?php

/*
 * Copyright © 2023 Mateusz Paluszek
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Support;

trait ClearDatabaseTrait
{
    use EntityManagerTrait;

    /**
     * Czyści tabele istniejące w bazie danych
     */
    private function clearDatabase(): void
    {
        $connection = $this->getEntityManager()
            ->getConnection();
        /* Czyszczenie tabel przed rozpoczęciem testów */
        $connection->executeStatement('DELETE FROM currency_rate');
        $connection->executeStatement('DELETE FROM currency');
    }
}
