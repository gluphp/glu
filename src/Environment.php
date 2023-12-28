<?php declare(strict_types = 1);

namespace Glu;

final class Environment {
    private bool $loaded = false;
    private array $values;

    public function __construct(string $directory)
    {
        $this->values = [
            'globals' => []
        ];

        $env = \parse_ini_file($directory . '/.env', true, \INI_SCANNER_RAW);

        if (false === $this->loaded) {
            foreach ($env as $sectionName => $sectionItems) {
                $this->values[$sectionName] = [];
                foreach ($sectionItems as $key => $value) {
                    if ($value === 'false') {
                        $this->values[$sectionName][$key] = false;
                    } elseif ($value === 'true') {
                        $this->values[$sectionName][$key] = true;
                    } elseif (\is_numeric($value)) {
                        $this->values[$sectionName][$key] = \intval($value);
                    } else {
                        $this->values[$sectionName][$key] = $value;
                    }

                }
            }

            $this->loaded = true;
        }
    }
    public function get(string $section, string $id)
    {
        return $this->values[$section][$id] ?? null;
    }

    public function all(string $section): array
    {
        return $this->values[$section] ?? [];
    }
}
