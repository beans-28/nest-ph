<?php

namespace App\Services;

/**
 * Decides a ticket's priority (critical / high / medium / low) on its own,
 * from the concern type the tenant picked plus the words in the subject
 * and details. Admins no longer tag priority by hand.
 *
 * How it decides, in order:
 *   1. Danger words (fire, smoke, gas leak, assault...) make ANY ticket
 *      Critical -- tenants don't always pick the right concern type in
 *      an emergency.
 *   2. The concern type's own rules, highest level first. The first rule
 *      whose words appear in the text wins.
 *   3. Nothing matched: the concern type's default level.
 *
 * Safe phrases like "fire drill" or "smoke detector" are ignored for
 * rule 1. Words are matched as whole words (case-insensitive), English plus
 * common Filipino/Taglish terms. Keyword matching can't understand
 * "there is NO smoke" -- it would still see "smoke". Leaning toward the
 * safer (higher) level is intended.
 */
class TicketPriorityClassifier
{
    public const LEVELS = ['critical', 'high', 'medium', 'low'];

    /** Rule 1: Critical regardless of concern type, grouped by what the danger is. */
    private const CRITICAL_ANY = [
        'Fire or smoke' => ['fire', 'on fire', 'smoke', 'smoking outlet', 'burning', 'burning smell', 'explosion',
            'sunog', 'nasusunog', 'usok', 'umuusok'],
        'Gas leak' => ['gas leak', 'smell of gas', 'leaking gas', 'amoy gas'],
        'Electrocution risk' => ['electrocuted', 'electrocution', 'nakuryente'],
        'Collapse risk' => ['collapse', 'collapsed', 'collapsing', 'gumuho', 'bumagsak ang kisame'],
        'Intruder or violence' => ['break-in', 'break in', 'broke in', 'breaking in', 'intruder', 'assault', 'assaulted',
            'attacked', 'weapon', 'knife', 'gun', 'stabbed', 'pinasok', 'may nanloob', 'sinaktan', 'binugbog'],
    ];

    /** Plain-language name for each concern-type rule, shown as the "why". */
    private const RULE_TOPICS = [
        'fire_safety_hazard' => ['critical' => 'Exposed live wires'],
        'plumbing_water_emergency' => ['critical' => 'Flooding or burst pipe', 'high' => 'No water or major leak'],
        'electrical_issue' => ['critical' => 'Sparks, shock or melting', 'high' => 'Power outage or exposed wiring'],
        'structural_damage' => ['critical' => 'Something about to fall', 'high' => 'Cracks, leaks or damaged ceiling/floor'],
        'security_concern' => ['critical' => 'Threat to a tenant', 'high' => 'Theft, intruder or broken lock'],
        'safety_security' => ['critical' => 'Threat to a tenant'],
        'maintenance_repairs' => ['high' => 'Repair is a safety risk', 'low' => 'Cosmetic or minor fix'],
        'facilities_amenities' => ['low' => 'Amenity request'],
        'noise_roommate_concern' => ['medium' => 'Repeated noise or roommate conflict'],
        'administrative_leasing_concern' => ['medium' => 'Contract or lease correction'],
    ];

    /**
     * Safe phrases that contain a danger word ("fire drill", "no smoking")
     * -- removed from the text before rule 1 looks at it.
     */
    private const SAFE_PHRASES = [
        'fire drill', 'fire exit', 'fire extinguisher', 'fire extinguishers', 'fire alarm test', 'fire safety',
        'no smoking', 'smoke detector', 'smoke detectors', 'smoking area',
    ];

    /**
     * Rule 2: per concern type, highest level first. Built from the
     * team's priority guide (critical / high / medium / low examples).
     */
    private const RULES = [
        'fire_safety_hazard' => [
            'critical' => ['live wire', 'live wires', 'exposed wire', 'exposed wires'],
        ],
        'plumbing_water_emergency' => [
            'critical' => ['flood', 'flooding', 'flooded', 'burst', 'burst pipe', 'pipe burst', 'baha', 'binabaha', 'pumutok ang tubo'],
            'high' => ['no water', 'walang tubig', 'severe leak', 'major leak', 'big leak', 'overflowing', 'overflow',
                'clogged toilet', 'barado', 'umaapaw', 'malakas na tagas'],
        ],
        'electrical_issue' => [
            'critical' => ['spark', 'sparks', 'sparked', 'sparking', 'shock', 'shocked', 'electric shock',
                'melted', 'melting', 'nag-spark', 'umaapoy', 'nakuryente'],
            'high' => ['power outage', 'no power', 'no electricity', 'blackout', 'brownout', 'exposed wire',
                'exposed wiring', 'walang kuryente', 'nawalan ng kuryente'],
        ],
        'structural_damage' => [
            'critical' => ['falling', 'about to fall', 'sagging ceiling', 'unstable', 'babagsak', 'bibigay'],
            'high' => ['large crack', 'big crack', 'cracks', 'damaged ceiling', 'ceiling damage', 'hole in the ceiling',
                'unsafe floor', 'broken floor', 'sinking floor', 'ceiling leak', 'leaking ceiling',
                'tumutulo', 'kisame', 'bitak', 'malaking bitak', 'butas sa kisame', 'sira ang sahig'],
        ],
        'security_concern' => [
            'critical' => ['threat', 'threatened', 'threatening', 'tinakot', 'pinagbantaan'],
            'high' => ['theft', 'stolen', 'stole', 'robbed', 'unauthorized', 'stranger', 'trespass', 'main door',
                'main entrance', 'gate lock', 'lock', 'locks', 'broken lock', 'cannot lock', 'nagla-lock', "can't lock", 'nawala', 'ninakaw',
                'nanakawan', 'magnanakaw', 'hindi nagla-lock', 'sira ang lock'],
        ],
        'safety_security' => [
            'critical' => ['threat', 'threatened', 'threatening', 'tinakot', 'pinagbantaan'],
        ],
        'maintenance_repairs' => [
            'high' => ['dangerous', 'unsafe', 'hazard', 'injured', 'injury', 'sharp', 'loose railing', 'broken stairs',
                'delikado', 'mapanganib', 'nasugatan'],
            'low' => ['cosmetic', 'paint', 'repaint', 'scratch', 'squeaky', 'squeaking', 'loose handle', 'minor',
                'adjust', 'adjustment', 'curtain', 'pintura', 'gasgas'],
        ],
        'facilities_amenities' => [
            'low' => ['request', 'requesting', 'suggest', 'can we have', 'add more', 'would like', 'sana', 'pwede po bang'],
        ],
        'noise_roommate_concern' => [
            'medium' => ['repeated', 'repeatedly', 'again', 'always', 'every night', 'nightly', 'nights', 'many times',
                'disagreement', 'argument', 'fight', 'conflict', 'roommate', 'palagi', 'lagi', 'gabi-gabi',
                'ilang beses', 'nag-away', 'kasama sa kwarto'],
        ],
        'administrative_leasing_concern' => [
            'medium' => ['dispute', 'disagree', 'correction', 'correct', 'wrong', 'error', 'mistake', 'contract',
                'lease', 'occupancy', 'mali', 'kontrata'],
        ],
    ];

    /** Rule 3: level when no words matched. */
    private const DEFAULTS = [
        'fire_safety_hazard' => 'high',
        'plumbing_water_emergency' => 'medium',
        'electrical_issue' => 'medium',
        'structural_damage' => 'medium',
        'security_concern' => 'medium',
        'safety_security' => 'high',
        'maintenance_repairs' => 'medium',
        'facilities_amenities' => 'medium',
        'billing_payment_concern' => 'medium',
        'administrative_leasing_concern' => 'low',
        'account_access_issue' => 'medium',
        'noise_roommate_concern' => 'low',
        'suggestion_feedback' => 'low',
        'tenant_report' => 'medium',
    ];

    /** "Report a Tenant": the reason picks the starting level. */
    private const REPORT_REASON_LEVELS = [
        'harassment_threats' => 'high',
        'theft_missing_items' => 'high',
        'unauthorized_visitors' => 'high',
        'property_damage' => 'medium',
        'house_rules_violation' => 'medium',
        'noise_disturbance' => 'low',
        'cleanliness_hygiene' => 'low',
        'other' => 'medium',
    ];

    /**
     * @return array{priority: string, reason: string}
     *   reason is a short plain-English "why", shown to admins.
     */
    public static function classify(string $category, ?string $title, ?string $description, ?string $reportReason = null): array
    {
        $text = mb_strtolower(trim(($title ?? '') . ' ' . ($description ?? '')));

        $dangerText = str_replace(self::SAFE_PHRASES, ' ', $text);

        foreach (self::CRITICAL_ANY as $topic => $words) {
            if ($word = self::firstMatch($dangerText, $words)) {
                return ['priority' => 'critical', 'reason' => self::because($topic, $word)];
            }
        }

        if ($category === 'tenant_report') {
            return self::classifyReport($text, $reportReason);
        }

        foreach (self::RULES[$category] ?? [] as $level => $words) {
            if ($word = self::firstMatch($text, $words)) {
                return ['priority' => $level, 'reason' => self::because(self::RULE_TOPICS[$category][$level] ?? 'Keyword', $word)];
            }
        }

        return ['priority' => self::DEFAULTS[$category] ?? 'medium', 'reason' => 'Standard for this concern type'];
    }

    private static function classifyReport(string $text, ?string $reason): array
    {
        if (in_array($reason, ['harassment_threats', 'theft_missing_items'], true)
            && ($word = self::firstMatch($text, self::RULES['security_concern']['critical']))) {
            return ['priority' => 'critical', 'reason' => self::because('Threat to a tenant', $word)];
        }

        // Noise reports use the same "repeated vs occasional" rule as the
        // Noise / Roommate concern type.
        if ($reason === 'noise_disturbance'
            && ($word = self::firstMatch($text, self::RULES['noise_roommate_concern']['medium']))) {
            return ['priority' => 'medium', 'reason' => self::because('Repeated noise', $word)];
        }

        return [
            'priority' => self::REPORT_REASON_LEVELS[$reason] ?? 'medium',
            'reason' => 'Standard for this report reason',
        ];
    }

    /** e.g. 'Fire or smoke · "usok"' -- the topic, then the word that triggered it. */
    private static function because(string $topic, string $word): string
    {
        return "{$topic} · \"{$word}\"";
    }

    private static function firstMatch(string $text, array $words): ?string
    {
        foreach ($words as $word) {
            $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/u';
            if (preg_match($pattern, $text)) {
                return $word;
            }
        }

        return null;
    }
}
