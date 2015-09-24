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

function zzdump($obj)
{
    if (true || in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', Configuration::get('PS_MAINTENANCE_IP')))) {
        echo 'XXX<br>';
        var_dump($obj);
        echo '<br>XXX';
    }
}

