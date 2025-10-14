<?php

namespace App\Repository;

use PDO;

abstract readonly class Repository
{
    public function __construct(
        protected PDO $pdo,
    ) {}
}
