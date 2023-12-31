<?php

namespace Glu\Extension\Mysql\Source;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Glu\DataSource\Source;
use Glu\DataSource\SourceFactory;

final class DbalSourceFactory implements SourceFactory
{
    public function supports(array $context): bool
    {
        return $context['type'] === 'mysql';
    }

    public function create(array $context): Source
    {
        $connectionParams = [
            'dbname' => $context['dbname'] ?? null,
            'user' => $context['user'] ?? null,
            'password' => $context['password'] ?? null,
            'host' => $context['host'] ?? null,
            'driver' => $context['driver'] ?? 'pdo_mysql',
        ];

        return new DbalSource(
            //DriverManager::getConnection((new DsnParser())->parse($dsn))
            DriverManager::getConnection($connectionParams)
        );
    }
}
