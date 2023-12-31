<?php

namespace Glu\Adapter\DataSource;

use Glu\DataSource\Source;
use Glu\DataSource\SourceFactory;

final class FilesystemSourceFactory implements SourceFactory
{
    public function supports(array $context): bool
    {
        return $context['type'] === 'fs';
    }

    public function create(array $context): Source
    {
        return new FilesystemSource($context['directory']);
    }

}
