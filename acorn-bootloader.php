<?php

use Roots\Acorn\Application;
use Sentry\Laravel\Integration;
use Roots\Acorn\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

/**
 * Plugin Name:  Acorn Bootloader
 * Plugin URI:   https://gitlab.com/aon21/acorn-bootloader
 * Description:  Acorn must be booted in order to use it. This plugin handles Acorn booting.
 * Version:      1.0.0
 * Author:       Mantas Tautvaisa
 * License:      MIT License
 */
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

function acorn_bootloader(): void
{
    $app_builder = Application::configure();
    $app_builder->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    });
    $app_builder->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            ValidateCsrfToken::class,
        ]);
    });
    $app_builder->boot();
}

add_action('after_setup_theme', 'acorn_bootloader');

function acorn_bootloader_start_session(): void
{
    $session = app('session');

    if (!$session->isStarted()) {
        if (!empty($_COOKIE[$session->getName()])) {
            $session->setId($_COOKIE[$session->getName()]);
        }
        $session->start();
    }
}

add_action('init', 'acorn_bootloader_start_session');

function acorn_bootloader_save_session(): void
{
    $session = app('session');

    if ($session->isStarted()) {
        $session->save();
    }
}

add_action('shutdown', 'acorn_bootloader_save_session');
