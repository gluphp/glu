<?php

declare(strict_types=1);

namespace Glu\DataSource;

interface SourceFactory
{
    public function supports(array $context): bool;
    public function create(array $context): Source;
}
