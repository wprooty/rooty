<?php

use Symfony\Component\HttpFoundation\Request;

if (defined('ROOTY_LOADED')) {
    return;
}

define('ROOTY_LOADED', true);

$rootyBase = dirname(__DIR__);
$theme     = wp_get_theme();
$themeDir  = get_theme_root($theme->stylesheet) . '/' . $theme->stylesheet;

if (str_starts_with(
    realpath($themeDir),
    realpath($rootyBase . '/public/themes')
)) {
    if (! defined('ROOTY_CLI')) {
        (require_once $rootyBase . '/bootstrap/app.php')
            ->handleRequest(Request::createFromGlobals());
    }
}
