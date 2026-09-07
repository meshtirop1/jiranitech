<?php

namespace App\Http\Controllers;

use App\Support\StructuredData;
use Illuminate\Http\Response;

/**
 * robots.txt.
 *
 * Served from a route rather than public/robots.txt so the sitemap line carries
 * whatever host the site is actually running on. The previous file was written
 * against a domain that has since been abandoned; a generated one cannot go stale
 * the same way.
 *
 * The console is disallowed as tidiness, not as a control: /admin is behind auth
 * and every one of its templates already carries "noindex, nofollow". robots.txt
 * is a public file and a crawler is free to ignore it, so it is never the thing
 * keeping anyone out.
 */
class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            '',
            // Signed, single-use confirmation pages. Indexing one would publish a
            // named enquiry from an organisation that sent it to us in confidence.
            'Disallow: /contact/request-for-proposal/',
            '',
            'Sitemap: '.StructuredData::canonical('sitemap.xml'),
            '',
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
