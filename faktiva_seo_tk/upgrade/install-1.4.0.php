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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_0($module)
{
    Configuration::deleteByName('ZZSEOTK_HREFLANG_ENABLED');
    Configuration::deleteByName('ZZSEOTK_CANONICAL_ENABLED');
    Configuration::deleteByName('ZZSEOTK_NOBOTS_ENABLED');

    Db::getInstance()->delete('module', "`name` = 'zzseotk'", 1);
    Db::getInstance()->delete('module_preference', "`module` = 'zzseotk'");
    Db::getInstance()->delete('configuration', "`name` LIKE 'zzseotk%'");
    Db::getInstance()->delete('quick_access', "`link` LIKE '%module_name=zzseotk%'");

    return true;
}
