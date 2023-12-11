<?php

namespace Glu\Adapter\DataSource;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Glu\DataSource\Source;

final class FilesystemSource implements Source
{
    public function __construct(
        private readonly string $baseDirectory
    ) {}

    public static function create(string $baseDirectory): static
    {
        return new self($baseDirectory);
    }

    public function insert(string $table, array $data)
    {
        $filePath = $this->baseDirectory.'/'.$table.'.csv';
        $file = fopen($filePath, 'a');
        fputcsv($file, $data);
        fclose($file);
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
