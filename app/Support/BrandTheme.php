<?php

namespace App\Support;

/**
 * Turns the dorm's one chosen brand color into every shade the site uses.
 *
 * Pages are styled with CSS variables (--green-accent, --sage-700, ...),
 * originally all green. We keep the chosen color's hue and saturation and
 * only change how light or dark it is per variable, so a dark button, a pale
 * hover background and the sidebar gradient all come from the same color.
 *
 * Status colors (green = vacant, red = overdue, etc.) are NOT touched: they
 * carry meaning, not branding.
 */
class BrandTheme
{
    /** The original NEST.PH green. Choosing it (or nothing) = no override. */
    public const DEFAULT = '#2f6f3c';

    /** Ready-made choices shown on the Dormitory Profile page. */
    public const PRESETS = [
        'Green (default)' => '#2f6f3c',
        'Gold' => '#c98a2b',
        'Copper' => '#c4773a',
        'Maroon' => '#8c2f39',
        'Blue' => '#2f5d8c',
        'Teal' => '#2a7a78',
        'Purple' => '#6a4c93',
        'Charcoal' => '#4a4f55',
    ];

    /** CSS variable => lightness (0-100) of the shade it gets. */
    private const SHADES = [
        // Shared admin/tenant names
        '--green-sidebar-top' => 40, '--green-sidebar-bottom' => 19,
        '--green-accent' => 33, '--green-btn' => 33, '--green-btn-hover' => 26,
        '--green-dark' => 35, '--green-mid' => 42, '--green-darker' => 27,
        '--green-deep' => 20, '--green-light' => 76,
        '--leaf' => 76, '--mint' => 70,
        '--chart-muted' => 82, '--chart-muted-hover' => 70,
        '--brand-tint' => 93,
        // tenant.css scale
        '--sage-900' => 19, '--sage-800' => 25, '--sage-700' => 31, '--sage-600' => 37,
        '--sage-500' => 50, '--sage-300' => 72, '--sage-200' => 84, '--sage-100' => 91, '--sage-50' => 96,
    ];

    public static function isValid(?string $hex): bool
    {
        return is_string($hex) && preg_match('/^#[0-9a-fA-F]{6}$/', $hex) === 1;
    }

    /** A <style> block overriding the brand variables, or '' for the default. */
    public static function styleTag(?string $hex): string
    {
        if (! self::isValid($hex) || strtolower($hex) === self::DEFAULT) {
            return '';
        }

        [$h, $s] = self::toHsl($hex);
        // Very grey picks stay grey; very loud picks get toned down so pale
        // backgrounds don't look neon.
        $s = min($s, 70);

        $vars = '';
        foreach (self::SHADES as $name => $lightness) {
            // Pale shades get less color so they read as a soft tint.
            $sat = $lightness >= 80 ? min($s, 45) : $s;
            $hex = self::fromHsl($h, $sat, $lightness);
            // Dark shades sit behind white text (sidebar, buttons) or are
            // used as text on white. Yellows and oranges look lighter than
            // greens at the same lightness, so darken until white-on-it is
            // readable (WCAG AA, 4.5:1).
            if ($lightness <= 45) {
                for ($l = $lightness; self::contrastWithWhite($hex) < 4.5 && $l > 5; $l--) {
                    $hex = self::fromHsl($h, $sat, $l);
                }
            }
            $vars .= $name.':'.$hex.';';
        }

        return '<style id="brand-theme">:root{'.$vars.'}</style>';
    }

    /** Contrast ratio of white text on this color (1 = none, 21 = max). */
    private static function contrastWithWhite(string $hex): float
    {
        $lin = fn ($c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        [$r, $g, $b] = array_map(fn ($c) => $lin(hexdec($c) / 255), str_split(substr($hex, 1), 2));
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return 1.05 / ($luminance + 0.05);
    }

    /** @return array{0: float, 1: float} hue (0-360), saturation (0-100) */
    private static function toHsl(string $hex): array
    {
        [$r, $g, $b] = array_map(fn ($c) => hexdec($c) / 255, str_split(substr($hex, 1), 2));
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        if ($d == 0) {
            return [0, 0];
        }
        $s = $d / (1 - abs(2 * $l - 1));
        $h = match ($max) {
            $r => fmod(($g - $b) / $d + 6, 6),
            $g => ($b - $r) / $d + 2,
            default => ($r - $g) / $d + 4,
        };

        return [$h * 60, $s * 100];
    }

    private static function fromHsl(float $h, float $s, float $l): string
    {
        $s /= 100;
        $l /= 100;
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        [$r, $g, $b] = match (true) {
            $h < 60 => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        return sprintf('#%02x%02x%02x', round(($r + $m) * 255), round(($g + $m) * 255), round(($b + $m) * 255));
    }
}
