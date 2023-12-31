<?php

namespace Glu\Adapter\DataSource;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Glu\DataSource\Source;

final class DbalSource implements Source
{
    public function __construct(
        private readonly \Doctrine\DBAL\Connection $connection
    ) {
    }

    public static function create(string $dsn): static
    {
        return new self(
            DriverManager::getConnection((new DsnParser())->parse($dsn))
        );
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
