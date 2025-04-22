<?php
/**
 * Plugin Name:  Acorn Bootloader
 * Plugin URI:   https://gitlab.com/aon21/acorn-bootloader
 * Description:  Acorn must be booted in order to use it. This plugin handles Acorn booting.
 * Version:      1.0.1
 * Author:       Mantas Tautvaisa
 * License:      GPLv2 or later
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 */
ob_start();

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

if (! function_exists('\Roots\bootloader')) {
    wp_die(
        __('You need to install Acorn to use this site.', 'domain'),
        '',
        [
            'link_url' => 'https://roots.io/acorn/docs/installation/',
            'link_text' => __('Acorn Docs: Installation', 'domain'),
        ]
    );
}

function startSession(): void
{
    $session = app('session');

    if (!$session->isStarted()) {
        if (!empty($_COOKIE[$session->getName()])) {
            $session->setId($_COOKIE[$session->getName()]);
        }
        $session->start();
    }

    session()->put('_persistent', 'true');
    session()->save();

    if (!headers_sent()) {
        setcookie(
            $session->getName(),
            $session->getId(),
            time() + (120 * 60),
            "/",
            "",
            isset($_SERVER['HTTPS']),
            true
        );
    }
}


add_action('init', function () {

    startSession();

    app('router')->pushMiddlewareToGroup('web', VerifyCsrfToken::class);
});

add_action('shutdown', function () {
    $session = app('session');

    if ($session->isStarted()) {
        $session->save();
    }

    if (ob_get_level() > 0) {
        ob_end_flush();
    }
});
