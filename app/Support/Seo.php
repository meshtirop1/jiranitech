<?php

namespace App\Support;

/**
 * Composes the title and description a search result actually shows.
 *
 * Both were assembled inline in the layout, which is how twenty-seven pages came
 * to carry titles between 61 and 88 characters. The page titles themselves are
 * fine — "Payment Gateway Integration & Custom Gateway Development" is 56 — but
 * the layout appended " — Jiranisoko Tech Solutions" to every one of them, and
 * Google stopped rendering the result somewhere in the middle of the brand name.
 *
 * The brand suffix is worth having when it fits and worth dropping when it does
 * not: a truncated title loses the end of the sentence that was doing the
 * ranking work, and on a deep service page the brand is already in the
 * breadcrumb, the URL and the visible chrome.
 */
class Seo
{
    /**
     * Google renders roughly 580px, which is about 60 characters at the widths
     * it uses. Titles are held under that rather than at some arbitrary count.
     */
    public const TITLE_MAX = 60;

    /** Descriptions are cut around 155–160; 155 leaves room for a wide font. */
    public const DESCRIPTION_MAX = 155;

    /**
     * The document title, with the brand appended only while it still fits.
     */
    public static function title(?string $pageTitle): string
    {
        $brand = (string) config('company.legal_name');

        if (blank($pageTitle)) {
            return self::fits($brand.' — Enterprise Technology')
                ? $brand.' — Enterprise Technology'
                : $brand;
        }

        $pageTitle = self::tidy($pageTitle);

        // In order of preference: the full brand, the short form, then nothing.
        // "Jiranisoko Tech Solutions" shortens to "Jiranisoko Tech", which is
        // still recognisable and buys ten characters.
        foreach ([$brand, self::shortBrand($brand)] as $suffix) {
            $candidate = $pageTitle.' — '.$suffix;

            if (self::fits($candidate)) {
                return $candidate;
            }
        }

        return $pageTitle;
    }

    /**
     * A description that ends where the writer ended a sentence, if one ends in
     * range, and at a word otherwise. Never mid-word: a snippet that stops in
     * the middle of "infrastruct" reads as a broken page rather than a long one.
     */
    public static function description(?string $text): ?string
    {
        $text = self::tidy((string) $text);

        if ($text === '') {
            return null;
        }

        if (mb_strlen($text) <= self::DESCRIPTION_MAX) {
            return $text;
        }

        $window = mb_substr($text, 0, self::DESCRIPTION_MAX);

        // Prefer a full stop, but only if it leaves a description worth having.
        $lastSentence = max(
            mb_strrpos($window, '. ') ?: -1,
            mb_strrpos($window, '! ') ?: -1,
            mb_strrpos($window, '? ') ?: -1,
        );

        // A complete short sentence beats a longer one that stops mid-thought, so
        // the bar for preferring the sentence break is low. It exists only to
        // stop a stray full stop near the start — an initial, an abbreviation —
        // reducing the description to a fragment.
        if ($lastSentence >= 60) {
            return rtrim(mb_substr($window, 0, $lastSentence + 1));
        }

        return rtrim(mb_substr($window, 0, mb_strrpos($window, ' ') ?: self::DESCRIPTION_MAX), ' ,;:—-').'…';
    }

    /** Whether a composed title is inside the rendered width. */
    public static function fits(string $title): bool
    {
        return mb_strlen($title) <= self::TITLE_MAX;
    }

    /**
     * The brand with its trailing generic word removed — "Jiranisoko Tech
     * Solutions" becomes "Jiranisoko Tech". Falls back to the full name when
     * there is nothing to trim.
     */
    private static function shortBrand(string $brand): string
    {
        $words = preg_split('/\s+/', trim($brand)) ?: [];

        return count($words) > 2 ? implode(' ', array_slice($words, 0, 2)) : $brand;
    }

    private static function tidy(string $text): string
    {
        return trim((string) preg_replace('/\s+/', ' ', strip_tags($text)));
    }
}
