<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * Serves the search console's HTML verification file.
 *
 * Google offers two ways to prove ownership. The meta tag is a paste; the HTML
 * file means downloading a file from Google and putting it in the document root,
 * which on this host means a File Manager upload — and if it is put anywhere
 * other than public_html, verification fails with a message that does not say
 * where it looked.
 *
 * So the file is answered rather than stored. The account owner pastes the file
 * name Google gave them into the console and the route below returns exactly
 * what Google expects to find at that address, which makes the file method the
 * same amount of work as the tag method and impossible to put in the wrong
 * place.
 *
 * The name has to match the configured one exactly. Anyone can guess the shape
 * of these file names, and answering all of them would let a stranger verify
 * ownership of this site in their own console.
 */
class SiteVerificationController extends Controller
{
    public function __invoke(string $file): Response
    {
        $expected = trim((string) config('company.verification.google_file'));

        abort_unless($expected !== '' && hash_equals($expected, $file), 404);

        // The body Google looks for is the file name, prefixed. Nothing else.
        return response("google-site-verification: {$file}")
            ->header('Content-Type', 'text/html; charset=utf-8');
    }
}
