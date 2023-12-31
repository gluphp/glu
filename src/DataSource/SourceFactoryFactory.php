<?php declare(strict_types = 1);

namespace Glu\DataSource;

final class SourceFactoryFactory {
    /** @var SourceFactory[] */
    private array $sourceFactories;

    public function __construct(array $sourceFactories) {
        $this->sourceFactories = $sourceFactories;
    }

    public function create(array $context): Source
    {
        foreach ($this->sourceFactories as $sourceFactory) {
            if ($sourceFactory->supports($context)) {
                return $sourceFactory->create($context);
            }
        }
    }
}
