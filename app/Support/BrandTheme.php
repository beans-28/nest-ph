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

    /**
     * Page background choices. Deliberately only light neutrals: every page
     * puts dark text straight on the background, so it must stay pale.
     * '' = keep each page's own default.
     */
    public const BACKGROUNDS = [
        'Default' => '',
        'White' => '#ffffff',
        'Linen' => '#faf6f0',
        'Cream' => '#f6efe2',
        'Beige' => '#efe4d3',
        'Sand' => '#e8dcc8',
        'Soft Grey' => '#f1efec',
    ];

    /** A <style> block setting the page background, or '' for the default. */
    public static function backgroundStyleTag(?string $hex): string
    {
        if (! is_string($hex) || $hex === '' || ! in_array($hex, self::BACKGROUNDS, true)) {
            return '';
        }

        [$h, $s, $l] = self::toHsl($hex);
        // The homepage's light bands fade from a slightly deeper shade.
        $deeper = self::fromHsl($h, $s, max(0, $l - 5));

        $css = ':root{--bg-page:'.$hex.';--cream:'.$deeper.';--cream-light:'.$hex.'}'
            .'html,body{background-color:'.$hex.'!important}'
            .'.location-section{background:'.$hex.'!important}';

        return '<style id="brand-background">'.$css.'</style>';
    }

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

    /**
     * Green colors typed straight into page styles (mostly the login, apply
     * and public pages) instead of using the variables above. The theme
     * swaps these in the page HTML too, or those pages would stay green-grey.
     */
    private const LITERALS = [
        '#567357', '#197335', '#44ad65', '#425542', '#194e19', '#a6b69f', '#4b5f4c', '#d8dcd8', '#a2d9a4',
        // Public Rooms / VR Tour pages: page background, prices, tags, buttons.
        '#4e7454', '#3b4a3e', '#92db9f', '#63856a', '#355e3d', '#e3efe3', '#2d5a35', '#1d4f27', '#3f533f', '#cfe9d2',
        '#1b3a1f', '#10301a', '#a9e6b4',
        // About the Dorm, Apply and Inquiry pages: headings, buttons, borders.
        '#d7e8d8', '#1f5130', '#345234', '#26401f', '#e2ede3', '#c5dbc7',
        // Not here on purpose: #00d444 (vacant-bed dot) and #2f7a3a ("verified" text) are status colors.
    ];

    /** Saturation of the default green; literals keep their saturation relative to it. */
    private const DEFAULT_SAT = 41;

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

        $p = self::params($hex);

        $vars = '';
        foreach (self::SHADES as $name => $base) {
            $vars .= $name.':'.self::shade($p, $base, self::DEFAULT_SAT).';';
        }

        $css = ':root{'.$vars.'}';

        // Light picks (pale yellow, pastel, ...): white text can't sit on
        // them, so the dark shades above stay dark for buttons and text. But
        // the big surfaces (sidebar, top bars) show the actual picked color,
        // with dark text on top, so the site visibly matches the pick.
        if ($p['light']) {
            [$h, $s, $l] = [$p['h'], $p['s'], $p['l']];
            $top = strtolower($hex); // exactly what the owner picked
            $bottom = self::fromHsl($h, $s, max(5, $l - 12));
            $ink = '#292420';
            $css .= '.sidebar{background:linear-gradient(180deg,'.$top.' 0%,'.$bottom.' 100%)!important;color:'.$ink.'!important}'
                .'.sidebar .nav-item,.sidebar .nav-item:hover,.sidebar .nav-item.active,.sidebar-section-label,.sidebar-logo,.sidebar a{color:'.$ink.'!important}'
                .'.sidebar .nav-item.active{background:rgba(0,0,0,.10)!important;border-left-color:'.$ink.'!important}'
                .'.sidebar .nav-item:hover{background:rgba(0,0,0,.06)!important}'
                .'.sidebar-footer .nav-item{background:rgba(0,0,0,.12)!important}'
                .'.sidebar-logo,.sidebar-footer{border-color:rgba(0,0,0,.12)!important}'
                .'.topbar,.topnav{background:linear-gradient(90deg,'.$bottom.','.$top.')!important}'
                .'.topbar .hamburger span{background:'.$ink.'!important}'
                .'.topbar-icon{background:rgba(255,255,255,.6)!important;color:'.$ink.'!important}'
                .'.topnav .logo,.topnav .logo:hover,.topnav .menu a,.topnav .btn-outline-white,.topnav .nav-toggle{color:'.$ink.'!important;border-color:'.$ink.'!important}'
                .'.topnav .menu a.pill{border-color:rgba(0,0,0,.4)!important}';
        }

        return '<style id="brand-theme">'.$css.'</style>';
    }

    /**
     * Swap the hard-coded greens in a page's HTML for the picked color's
     * matching shades. Returns the HTML unchanged for the default green.
     */
    public static function recolorHtml(string $html, ?string $hex): string
    {
        if (! self::isValid($hex) || strtolower($hex) === self::DEFAULT) {
            return $html;
        }

        $p = self::params($hex);
        $map = [];
        foreach (self::LITERALS as $literal) {
            [, $ls, $ll] = self::toHsl($literal);
            $color = self::shade($p, $ll, $ls);
            $map[$literal] = $color;
            $map[strtoupper($literal)] = $color;
        }

        return strtr($html, $map);
    }

    /** Everything derived once from the picked color. */
    private static function params(string $hex): array
    {
        [$h, $s, $l] = self::toHsl($hex);
        // Very grey picks stay grey; very loud picks get toned down so pale
        // backgrounds don't look neon.
        $s = min($s, 70);
        // The shade table was built around the default green (lightness 31).
        // Shift every shade by how much lighter/darker the pick is, so a
        // light color gives a lighter theme instead of always the same depth.
        $shift = $l - 31;
        // Don't shift dark shades so far that white text on them becomes
        // unreadable; moving them together keeps the gradient's depth.
        $darkShift = $shift;
        while ($darkShift > -20 && self::contrastWithWhite(self::fromHsl($h, $s, 42 + $darkShift)) < 3) {
            $darkShift--;
        }

        return [
            'h' => $h, 's' => $s, 'l' => $l, 'shift' => $shift, 'darkShift' => $darkShift,
            'light' => self::contrastWithWhite(self::fromHsl($h, $s, $l)) < 3,
        ];
    }

    /**
     * The picked color's version of a shade that had lightness $base (and
     * saturation $baseSat) in the default green theme.
     */
    private static function shade(array $p, float $base, float $baseSat): string
    {
        $h = $p['h'];
        $lightness = max(5, min(97, $base + ($base <= 45 ? $p['darkShift'] : $p['shift'] / 3)));
        // Greyish originals stay greyish (relative to the default green).
        $sat = min(100, $p['s'] * min(1.2, $baseSat / self::DEFAULT_SAT));
        // Pale shades get less color so they read as a soft tint.
        if ($lightness >= 80) {
            $sat = min($sat, 45);
        }
        $hex = self::fromHsl($h, $sat, $lightness);
        // Dark shades sit behind white text (sidebar, buttons) or are used
        // as text on white. Yellows and oranges look lighter than greens at
        // the same lightness, so darken until white-on-it is readable.
        // 3:1 is the minimum for bold/large text and buttons.
        if ($base <= 45) {
            for ($l = $lightness; self::contrastWithWhite($hex) < 3 && $l > 5; $l--) {
                $hex = self::fromHsl($h, $sat, $l);
            }
        }

        return $hex;
    }

    /** Contrast ratio of white text on this color (1 = none, 21 = max). */
    private static function contrastWithWhite(string $hex): float
    {
        $lin = fn ($c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        [$r, $g, $b] = array_map(fn ($c) => $lin(hexdec($c) / 255), str_split(substr($hex, 1), 2));
        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return 1.05 / ($luminance + 0.05);
    }

    /** @return array{0: float, 1: float, 2: float} hue (0-360), saturation, lightness (0-100) */
    private static function toHsl(string $hex): array
    {
        [$r, $g, $b] = array_map(fn ($c) => hexdec($c) / 255, str_split(substr($hex, 1), 2));
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        if ($d == 0) {
            return [0, 0, $l * 100];
        }
        $s = $d / (1 - abs(2 * $l - 1));
        $h = match ($max) {
            $r => fmod(($g - $b) / $d + 6, 6),
            $g => ($b - $r) / $d + 2,
            default => ($r - $g) / $d + 4,
        };

        return [$h * 60, $s * 100, $l * 100];
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
