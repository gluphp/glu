<?php declare(strict_types = 1);

namespace Glu\Templating;

use Glu\Http\Request;

interface TemplateRenderer {
    public function render(string $path, Request $request, array $context = []): string;

    public function registerFunction(_Function $function): void;
}
