<?php declare(strict_types = 1);

namespace Glu\DataSource;

final class SourceFactoryFactory implements SourceFactory {
    /** @var SourceFactory[] */
    private array $sourceFactories;

    public function __construct(array $sourceFactories) {
        $this->sourceFactories = $sourceFactories;
    }

    public function create(array $context): Source
    {
        foreach ($this->sourceFactories as $source) {
            if ($source->supports($context)) {
                return $source;
            }
        }
    }
}
