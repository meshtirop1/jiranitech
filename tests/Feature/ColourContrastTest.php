<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The site publishes a WCAG 2.2 AA conformance target at /legal/accessibility-statement.
 * These tests hold the palette to it, so a later colour tweak cannot quietly turn that
 * statement into a false claim.
 *
 * The palette is devcom.com's, measured from their pages. One colour could not be taken
 * as found: white on their #1CBBED is 2.26:1, which is what their buttons and sticky
 * header use. --cyan-deep is that hue at a value white can sit on, and #1CBBED itself is
 * kept for fills, rules, bullets and anything carrying black text.
 *
 * Ratios are computed from the token values in resources/css/app.css using the WCAG 2.x
 * relative luminance formula.
 */
class ColourContrastTest extends TestCase
{
    /**
     * @return array<string, string>
     */
    private static function tokens(): array
    {
        $css = file_get_contents(base_path('resources/css/app.css'));

        preg_match('/^:root \{(.*?)^\}/ms', $css, $m);
        preg_match_all('/--([a-z0-9-]+):\s*(#[0-9a-fA-F]{3,8})\s*;/', $m[1] ?? '', $pairs, PREG_SET_ORDER);

        $tokens = [];
        foreach ($pairs as $p) {
            $tokens[$p[1]] = $p[2];
        }

        return $tokens;
    }

    private static function luminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $channel = function (int $v): float {
            $v /= 255;

            return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        };

        return 0.2126 * $channel((int) hexdec(substr($hex, 0, 2)))
            + 0.7152 * $channel((int) hexdec(substr($hex, 2, 2)))
            + 0.0722 * $channel((int) hexdec(substr($hex, 4, 2)));
    }

    private static function ratio(string $a, string $b): float
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    /**
     * Text pairs that must clear 4.5:1 (SC 1.4.3, normal-size text).
     *
     * @return array<string, array{string, string}>
     */
    public static function textPairs(): array
    {
        $cases = [];

        foreach ([
            // Body and secondary copy on each of the three grounds.
            ['ink', 'ground'],
            ['ink', 'surface'],
            ['ink', 'surface-2'],
            ['ink-strong', 'surface'],
            ['muted', 'ground'],
            ['muted', 'surface'],
            ['muted', 'surface-2'],

            // Links, buttons and the accent wherever it carries text.
            ['cyan-deep', 'ground'],
            ['cyan-deep', 'surface'],
            ['cyan-deep', 'surface-2'],
            ['cyan-deep', 'cyan-soft'],
            ['white', 'cyan-deep'],

            // The full-width accent band puts black on the undiluted cyan.
            ['black', 'cyan'],

            // Status colours, which have to be legible as well as distinct.
            ['positive', 'surface'],
            ['positive', 'positive-soft'],
            ['negative', 'surface'],
            ['negative', 'negative-soft'],

            // Permanently dark grounds: the top strip and the page hero.
            ['utility-ink', 'utility-ground'],
            ['hero-ink', 'hero-ground'],
            ['cyan', 'hero-ground'],
        ] as [$fg, $bg]) {
            $cases["{$fg} on {$bg}"] = [$fg, $bg];
        }

        return $cases;
    }

    #[DataProvider('textPairs')]
    public function test_text_pairs_meet_wcag_aa(string $fg, string $bg): void
    {
        $tokens = self::tokens();

        $this->assertArrayHasKey($fg, $tokens, "Missing token --{$fg}");
        $this->assertArrayHasKey($bg, $tokens, "Missing token --{$bg}");

        $ratio = self::ratio($tokens[$fg], $tokens[$bg]);

        $this->assertGreaterThanOrEqual(
            4.5,
            round($ratio, 2),
            sprintf(
                '--%s (%s) on --%s (%s) is %.2f:1, below the 4.5:1 AA minimum for normal text.',
                $fg, $tokens[$fg], $bg, $tokens[$bg], $ratio,
            ),
        );
    }

    /**
     * SC 1.4.11 Non-text Contrast: the visible boundary of a form control must reach
     * 3:1 against the surface behind it. The field fill is the same white as the page,
     * so the border is the only thing identifying the control.
     */
    public function test_form_control_borders_meet_non_text_contrast(): void
    {
        $tokens = self::tokens();

        foreach (['surface', 'ground', 'surface-2'] as $behind) {
            $ratio = self::ratio($tokens['field-border'], $tokens[$behind]);

            $this->assertGreaterThanOrEqual(
                3.0,
                round($ratio, 2),
                sprintf(
                    '--field-border (%s) on --%s (%s) is %.2f:1, below the 3:1 SC 1.4.11 minimum.',
                    $tokens['field-border'], $behind, $tokens[$behind], $ratio,
                ),
            );
        }
    }

    /**
     * The two statuses sit at a similar lightness on purpose, so that neither reads
     * as louder than the other; what separates them is hue. Colour is never the only
     * signal — every gate and every field also states its status in words — but the
     * two must not drift into the same colour.
     */
    public function test_the_two_status_colours_stay_far_apart_in_hue(): void
    {
        $tokens = self::tokens();

        $hue = function (string $hex): float {
            [$r, $g, $b] = array_map(
                fn ($p) => hexdec($p) / 255,
                str_split(ltrim($hex, '#'), 2)
            );

            $max = max($r, $g, $b);
            $min = min($r, $g, $b);
            $c = $max - $min;

            if ($c == 0.0) {
                return 0.0;
            }

            $h = match ($max) {
                $r => fmod((($g - $b) / $c), 6),
                $g => (($b - $r) / $c) + 2,
                default => (($r - $g) / $c) + 4,
            };

            return fmod(($h * 60) + 360, 360);
        };

        $separation = abs($hue($tokens['positive']) - $hue($tokens['negative']));
        $separation = min($separation, 360 - $separation);

        $this->assertGreaterThan(
            60,
            $separation,
            sprintf(
                'The positive (%s) and negative (%s) states are only %.0f degrees apart in hue.',
                $tokens['positive'], $tokens['negative'], $separation,
            ),
        );
    }

    /**
     * The top strip and the page hero are dark grounds carrying white type. Building
     * them from a token that is light would put white on white.
     */
    public function test_fixed_dark_grounds_stay_dark(): void
    {
        $tokens = self::tokens();

        foreach (['utility-ground', 'hero-ground'] as $token) {
            $this->assertLessThan(
                0.18,
                self::luminance($tokens[$token]),
                sprintf('--%s (%s) is not a dark ground.', $token, $tokens[$token]),
            );
        }
    }

    /**
     * #1CBBED is the brand accent exactly as devcom.com sets it, and it is too light
     * for white text. This asserts the reason --cyan-deep exists, so nobody later
     * "simplifies" the two back into one.
     */
    public function test_the_undiluted_accent_is_never_asked_to_carry_white_text(): void
    {
        $tokens = self::tokens();

        $this->assertLessThan(
            4.5,
            self::ratio($tokens['white'], $tokens['cyan']),
            'If #1CBBED now clears 4.5:1 against white, --cyan-deep is no longer needed.',
        );

        $this->assertGreaterThanOrEqual(
            4.5,
            round(self::ratio($tokens['white'], $tokens['cyan-deep']), 2),
            '--cyan-deep must be dark enough to carry white text.',
        );
    }

    public function test_the_focus_ring_is_declared_once_and_uses_a_token(): void
    {
        $css = file_get_contents(base_path('resources/css/app.css'));

        $this->assertSame(
            1,
            substr_count($css, 'outline: 2px solid var(--cyan-deep)'),
            'The focus ring should be declared exactly once, from a token.',
        );

        // :where() has zero specificity, so any other outline rule would silently win.
        $this->assertSame(
            1,
            preg_match_all('/^\s*outline:/m', $css),
            'A second outline declaration would override the zero-specificity focus rule.',
        );
    }

    /**
     * The tracking, the monospace label alias and the sub-13px type were ours, not
     * DevCom's, and together they were most of why the pages read as templated.
     */
    public function test_the_stylesheet_carries_no_tracking_or_second_typeface(): void
    {
        $css = file_get_contents(base_path('resources/css/app.css'));

        $this->assertSame(
            0,
            preg_match_all('/letter-spacing:\s*-?0?\.\d+em/', $css),
            'devcom.com sets no letter-spacing anywhere; PT Sans should sit at its natural width.',
        );

        $this->assertSame(
            0,
            preg_match_all('/font-family:\s*var\(--font-mono\)/', $css),
            'There is one typeface site-wide. The mono alias only ever marked labels as different.',
        );
    }
}
