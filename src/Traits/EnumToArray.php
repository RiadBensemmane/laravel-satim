<?php

declare(strict_types=1);

namespace LaravelSatim\Traits;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 * @project laravel-satim
 * @package LaravelSatim\Traits
 * @name EnumToArray
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 * @version 1.0.0
 *
 * @method static cases
 */
trait EnumToArray
{
    /**
     * @return array
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public static function values(): array
    {
        if (method_exists(__CLASS__, 'cases')) {
            return array_column(static::cases(), 'value');
        }

        return [];
    }

    /**
     * @param string $name
     * @return static|null
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public static function fromName(string $name): ?static
    {
        $name = strtoupper($name);
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null;
    }
}
