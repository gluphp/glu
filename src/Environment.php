<?php declare(strict_types = 1);

namespace Glu;

final class Environment {
    private static bool $loaded = false;
    private static array $values;

    public static function load()
    {
        self::$values = [
            'globals' => []
        ];

        $env = \parse_ini_file(__DIR__ . '/../../../../.env', true, \INI_SCANNER_RAW);

        if (false === self::$loaded) {
            foreach ($env as $sectionName => $sectionItems) {
                self::$values[$sectionName] = [];
                foreach ($sectionItems as $key => $value) {
                    if ($value === 'false') {
                        self::$values[$sectionName][$key] = false;
                    } elseif ($value === 'true') {
                        self::$values[$sectionName][$key] = true;
                    } elseif (\is_numeric($value)) {
                        self::$values[$sectionName][$key] = \intval($value);
                    } else {
                        self::$values[$sectionName][$key] = $value;
                    }

                }
            }

            self::$loaded = true;
        }
    }
    public static function get(string $section, string $id)
    {
        self::load();

        return self::$values[$section][$id] ?? null;
    }

    public static function all(string $section): array
    {
        self::load();
        return self::$values[$section] ?? [];
    }
}
