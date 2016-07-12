<?php

/**
 *
 * NOTICE OF LICENSE
 * This source file is subject to the License terms Academi cFined in the file LICENSE.md
 *
 * DISCLAIMER
 * This code is provided as is without any warranty.
 * No promise of being safe or secure
 *
 * @author   ZiZuu.com <info@zizuu.com>
 * @link     source available at https://github.com/ZiZuu-store/
 */

// Set true to enable debugging
define('ZZ_DEBUG', false);

if (defined('ZZ_DEBUG') && ZZ_DEBUG && is_readable(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
    Symfony\Component\Debug\Debug::enable();
}
