import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

// One typeface across the whole site: PT Sans, as devcom.com uses. PT Sans ships
// only 400 and 700, so declared weights are held to those two rather than relying on
// the browser to synthesise 500 or 600.
//
// Fonts are self-hosted from the build output rather than requested from a third
// party at page load. The site makes data-protection claims; shipping every visitor's
// IP address to an external font host would undercut them.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('PT Sans', { weights: [400, 700], italic: true }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
