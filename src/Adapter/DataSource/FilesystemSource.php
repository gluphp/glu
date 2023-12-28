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
        if (\is_dir($this->baseDirectory) === false) {
            mkdir(directory: $this->baseDirectory, recursive: true);
        }
        $filePath = $this->baseDirectory.'/'.$table.'.csv';
        $fileExists = \file_exists($filePath);
        $file = fopen($filePath, 'a+');
        if ($fileExists === false) {
            fputcsv($file, array_keys($data));
        }
        fputcsv($file, $data);
        fclose($file);
    }

    public function update(string $table, array $data, array $criteria)
    {
        $this->connection->update($table, $data, $criteria);
    }

    public function fetch(string $query, array $context = []): array
    {
        $result = [];

        $filePath = $this->baseDirectory.'/'.$query.'.csv';
        $file = fopen($filePath, 'r');
        $keys = [];
        if ($file) {
            $firstLine = true;
            while (($line = fgetcsv($file)) !== false) {
                if ($firstLine) {
                    $keys = $line;
                    $firstLine = false;
                } else {
                    $result[] = \array_combine(
                        $keys,
                        $line
                    );
                }
            }
        }

        fclose($file);
        return $result;
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
