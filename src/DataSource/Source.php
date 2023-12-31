<?php

declare(strict_types=1);

namespace Glu\DataSource;

interface Source
{
    public function fetch(string $query, array $context = []): array;

    public function fetchOne(string $query, array $context = []): null|array;

    public function insert(string $table, array $data);
    public function update(string $table, array $data, array $criteria);
}
