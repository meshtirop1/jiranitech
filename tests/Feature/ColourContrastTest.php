<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The site publishes a WCAG 2.2 AA conformance target at /legal/accessibility-statement.
 * These tests hold the palette to it, so a later colour tweak cannot quietly turn that
 * statement into a false claim.
 *
 * Ratios are computed from the token values in resources/css/app.css using the WCAG 2.x
 * relative luminance formula.
 */
class ColourContrastTest extends TestCase
{
    /**
     * @return array<string, string>
     */
    private static function tokens(string $theme): array
    {
        $css = file_get_contents(base_path('resources/css/app.css'));

        // The bare :root block is the light palette; the [data-theme='dark'] block is dark.
        if ($theme === 'dark') {
            preg_match("/:root\[data-theme='dark'\]\s*\{(.*?)\}/s", $css, $m);
        } else {
            preg_match('/^:root \{(.*?)^\}/ms', $css, $m);
        }

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
     * @return array<string, array{string, string, string}>
     */
    public static function textPairs(): array
    {
        $cases = [];

        foreach (['light', 'dark'] as $theme) {
            foreach ([
                ['ink', 'ground'],
                ['ink', 'surface'],
                ['ink-2', 'ground'],
                ['ink-2', 'surface'],
                ['muted', 'ground'],
                ['muted', 'surface'],
                ['muted', 'surface-2'],
                ['brass', 'ground'],
                ['brass', 'surface-2'],
                ['brass', 'brass-soft'],
                ['pine', 'ground'],
                ['pine', 'surface'],
                ['clay', 'clay-soft'],
                ['pine-ink', 'pine-soft'],
                ['utility-ink', 'utility-ground'],
                ['hero-ink', 'hero-ground'],
            ] as [$fg, $bg]) {
                $cases["{$theme}: {$fg} on {$bg}"] = [$theme, $fg, $bg];
            }
        }

        return $cases;
    }

    #[DataProvider('textPairs')]
    public function test_text_pairs_meet_wcag_aa(string $theme, string $fg, string $bg): void
    {
        $tokens = self::tokens($theme);

        $this->assertArrayHasKey($fg, $tokens, "Missing token --{$fg} in {$theme} palette");
        $this->assertArrayHasKey($bg, $tokens, "Missing token --{$bg} in {$theme} palette");

        $ratio = self::ratio($tokens[$fg], $tokens[$bg]);

        $this->assertGreaterThanOrEqual(
            4.5,
            round($ratio, 2),
            sprintf(
                '%s: --%s (%s) on --%s (%s) is %.2f:1, below the 4.5:1 AA minimum for normal text.',
                $theme, $fg, $tokens[$fg], $bg, $tokens[$bg], $ratio,
            ),
        );
    }

    /**
     * SC 1.4.11 Non-text Contrast: the visible boundary of a form control must reach
     * 3:1 against the surface behind it. The field fill is near-identical to that
     * surface, so the border is the only thing identifying the control.
     */
    public function test_form_control_borders_meet_non_text_contrast(): void
    {
        foreach (['light', 'dark'] as $theme) {
            $tokens = self::tokens($theme);

            foreach (['surface', 'ground'] as $behind) {
                $ratio = self::ratio($tokens['field-border'], $tokens[$behind]);

                $this->assertGreaterThanOrEqual(
                    3.0,
                    round($ratio, 2),
                    sprintf(
                        '%s: --field-border (%s) on --%s (%s) is %.2f:1, below the 3:1 SC 1.4.11 minimum.',
                        $theme, $tokens['field-border'], $behind, $tokens[$behind], $ratio,
                    ),
                );
            }
        }
    }

    /**
     * The utility bar and the page hero must stay dark grounds in BOTH themes. Building
     * them from --ink or --pine-ink previously inverted them in dark mode, putting white
     * text on a light mint hero.
     */
    public function test_fixed_dark_grounds_stay_dark_in_both_themes(): void
    {
        foreach (['light', 'dark'] as $theme) {
            $tokens = self::tokens($theme);

            foreach (['utility-ground', 'hero-ground'] as $token) {
                $this->assertLessThan(
                    0.18,
                    self::luminance($tokens[$token]),
                    sprintf('%s: --%s (%s) is not a dark ground.', $theme, $token, $tokens[$token]),
                );
            }
        }
    }

    public function test_the_focus_ring_is_declared_once_and_uses_a_token(): void
    {
        $css = file_get_contents(base_path('resources/css/app.css'));

        $this->assertSame(
            1,
            substr_count($css, 'outline: 2px solid var(--brass)'),
            'The focus ring should be declared exactly once, from a token.',
        );

        // :where() has zero specificity, so any other outline rule would silently win.
        $this->assertSame(
            1,
            preg_match_all('/^\s*outline:/m', $css),
            'A second outline declaration would override the zero-specificity focus rule.',
        );
    }
}
