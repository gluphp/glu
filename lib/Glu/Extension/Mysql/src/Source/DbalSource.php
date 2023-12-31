<?php

namespace Glu\Extension\Mysql\Source;

use Doctrine\DBAL\Connection;
use Glu\DataSource\Source;

final class DbalSource implements Source
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function insert(string $table, array $data)
    {
        $this->connection->insert($table, $data);
    }

    public function update(string $table, array $data, array $criteria)
    {
        $this->connection->update($table, $data, $criteria);
    }

    public function fetch(string $query, array $context = []): array
    {
        return $this->connection->fetchAllAssociative($query, $context);
    }

    public function fetchOne(string $query, array $context = []): null|array
    {
        $result = $this->connection->fetchAssociative($query, $context);

        if (false === $result) {
            return null;
        }

        return $result;
    }
}
