<?php

/*
 * This file is part of the "Prestashop SEO ToolKit" module.
 *
 * (c) Faktiva (http://faktiva.com)
 *
 * NOTICE OF LICENSE
 * This source file is subject to the CC BY-SA 4.0 license that is
 * available at the URL https://creativecommons.org/licenses/by-sa/4.0/
 *
 * DISCLAIMER
 * This code is provided as is without any warranty.
 * No promise of being safe or secure
 *
 * @author   AlberT <albert@faktiva.com>
 * @license  https://creativecommons.org/licenses/by-sa/4.0/  CC-BY-SA-4.0
 * @source   https://github.com/faktiva/prestashop-seo-tk
 */

// Set true to enable debugging
define('ZZ_DEBUG', false);

if (defined('ZZ_DEBUG') && ZZ_DEBUG && is_readable(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
    Symfony\Component\Debug\Debug::enable();
}
